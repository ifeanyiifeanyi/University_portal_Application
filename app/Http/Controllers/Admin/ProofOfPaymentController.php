<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\ProveOfPayment;
use App\Models\PaymentInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ManualPaymentService;
use App\Http\Requests\processManualPaymentRequest;
use App\Notifications\AdminManualPaymentVerificationNotice;
use App\Notifications\StudentManualPaymentVerificationNotice;

class ProofOfPaymentController extends Controller
{

    protected $manualPaymentService;
    public function __construct(ManualPaymentService $manualPaymentService)
    {
        $this->manualPaymentService = $manualPaymentService;
    }


    /**
     * Verify a manual installment payment
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verifyManualInstallment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'installment_id' => 'required|exists:payment_installments,id',
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'transaction_reference' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'additional_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Get the payment and installment records
            $payment = Payment::findOrFail($request->payment_id);
            // dd($payment->invoice);
            $installment = PaymentInstallment::findOrFail($request->installment_id);

            // Validate this installment is pending and belongs to this payment
            if ($installment->payment_id != $payment->id) {
                throw new \Exception('Installment does not belong to this payment');
            }

            if ($installment->status != 'pending' && $installment->status != 'overdue') {
                throw new \Exception('This installment is already processed');
            }

            // Handle proof file upload
            $proofPath = $request->file('proof_file')->store('payment_proofs', 'public');

            // Update the installment record
            $installment->update([
                'status' => 'paid',
                'paid_amount' => $request->amount,
                'paid_at' => now()
            ]);

            // Calculate new totals
            $completedInstallments = $payment->installments()->where('status', 'paid')->get();
            $pendingInstallments = $payment->installments()->whereIn('status', ['pending', 'overdue'])->get();
            $totalPaid = $completedInstallments->sum('paid_amount');
            $remainingAmount = $payment->amount - $totalPaid;

            // Update the payment record
            $paymentUpdateData = [
                'base_amount' => $totalPaid,
                'remaining_amount' => $remainingAmount,
                'transaction_reference' => $request->transaction_reference,
                'payment_channel' => 'manual',
                'payment_reference' => $payment->invoice->invoice_number,
                'invoice_id' => $payment->invoice->id,
                'payment_proof' => $proofPath,
                'admin_comment' => $request->additional_notes ?? null,
                'is_manual' => true,

            ];

            // Check if this was the last installment
            if ($pendingInstallments->isEmpty()) {
                $paymentUpdateData['status'] = 'paid';
                $paymentUpdateData['installment_status'] = 'completed';
                $paymentUpdateData['next_installment_date'] = null;
                // $paymentUpdateData['next_transaction_amount'] = 0;
            } else {
                // Get next pending installment
                $nextPending = $pendingInstallments->sortBy('installment_number')->first();
                $paymentUpdateData['status'] = 'partial';
                $paymentUpdateData['installment_status'] = 'partial';
                $paymentUpdateData['next_installment_date'] = $nextPending->due_date;
                // $paymentUpdateData['next_transaction_amount'] = $nextPending->amount;
            }

            // Update the payment
            $payment->update($paymentUpdateData);

            // Create proof of payment record
            ProveOfPayment::create([
                'invoice_id' => $payment->invoice->id,
                'payment_type_id' => $payment->payment_type_id,
                'payment_method_id' => $payment->payment_method_id,
                'amount' => $request->amount,
                'transaction_reference' => $request->transaction_reference,
                'bank_name' => $request->bank_name ?? null,
                'proof_file' => $proofPath,
                'additional_notes' => $request->additional_notes ?? null,
                'metadata' => json_encode([
                    'installment_number' => $installment->installment_number,
                    'verified_by' => request()->user()->full_name
                ]),
                'status' => 'paid'
            ]);

            // Generate receipt for this installment
            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'REC' . uniqid(),
                'amount' => $request->amount,
                'date' => now(),
                'is_installment' => true,
                'installment_number' => $installment->installment_number,
                'total_amount' => $payment->amount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $pendingInstallments->isEmpty() ? 'paid' : 'partial'
            ]);

            // Update invoice status if necessary
            if ($pendingInstallments->isEmpty()) {
                $payment->invoice->update(['status' => 'paid']);
                $payment->invoice->update(['amount' => $payment->amount]);
            } else {
                $payment->invoice->update(['status' => 'partial']);
            }

            // Log the activity
            activity()
                ->performedOn($payment)
                ->causedBy(request()->user())
                ->withProperties([
                    'invoice_number' => $payment->invoice->invoice_number,
                    'amount' => $request->amount,
                    'installment_number' => $installment->installment_number,
                    'payment_id' => $payment->id
                ])
                ->log('Manual installment payment verified');

            // Send notifications
            $payment->invoice->student->user->notify(new StudentManualPaymentVerificationNotice($payment, $payment->status));

            User::admins()
                ->whereHas('admin', function ($query) {
                    $query->where('role', 'superAdmin');
                })
                ->each(function ($admin) use ($payment) {
                    $admin->notify(new AdminManualPaymentVerificationNotice($payment, $payment->status));
                });

            DB::commit();

            return redirect()->route('admin.payments.showReceipt', $receipt->id)
                ->with('success', 'Installment ' . $installment->installment_number . ' verified successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual installment verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to verify installment: ' . $e->getMessage());
        }
    }


    /**
     * Verify a manual full payment
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verifyManualPayment(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'transaction_reference' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'additional_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Get the invoice
            $invoice = Invoice::findOrFail($request->invoice_id);

            // Check if payment already exists
            $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();

            if (!$payment) {
                // Create new payment record
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'student_id' => $invoice->student_id,
                    'level' => $invoice->level,
                    'department_id' => $invoice->department_id,
                    'payment_type_id' => $invoice->payment_type_id,
                    'payment_method_id' => $invoice->payment_method_id ?? 1, // Default to manual method
                    'academic_session_id' => $invoice->academic_session_id,
                    'semester_id' => $invoice->semester_id,
                    'amount' => $invoice->amount,
                    'base_amount' => $request->amount,
                    'transaction_reference' => $request->transaction_reference,
                    'is_installment' => false,
                    'is_manual' => true,
                    'remaining_amount' => 0,
                    'next_installment_date' => null,
                    'next_transaction_amount' => 0,
                    'status' => 'paid',
                    'payment_date' => now(),
                    'payment_channel' => 'manual',
                    'payment_reference' => $invoice->invoice_number,
                    'payment_proof' => $request->file('proof_file')->store('payment_proofs', 'public'),
                    'admin_comment' => $request->additional_notes ?? null,
                    'admin_id' => Auth::user()->id,
                ]);
            } else {
                // Update existing payment
                $payment->update([
                    'amount' => $invoice->amount,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'student_id' => $invoice->student_id,
                    'payment_type_id' => $invoice->payment_type_id,
                    'academic_session_id' => $invoice->academic_session_id,
                    'semester_id' => $invoice->semester_id,
                    'base_amount' => $request->amount,
                    'transaction_reference' => $request->transaction_reference,
                    'status' => 'paid',
                    'payment_date' => now(),
                    'admin_comment' => $request->additional_notes ?? null,
                    'payment_proof' => $request->file('proof_file')->store('payment_proofs', 'public'),
                    'payment_channel' => 'manual',
                    'payment_reference' => $invoice->invoice_number,
                    'is_manual' => true,
                    'payment_method_id' => $invoice->payment_method_id ?? 1, // Default to manual method
                ]);
            }

            // Handle proof file upload
            $proofPath = $request->file('proof_file')->store('payment_proofs', 'public');

            // Create proof of payment record
            ProveOfPayment::create([
                'invoice_id' => $invoice->id,
                'payment_type_id' => $invoice->payment_type_id,
                'payment_method_id' => $payment->payment_method_id,
                'amount' => $request->amount,
                'transaction_reference' => $request->transaction_reference,
                'bank_name' => $request->bank_name ?? null,
                'proof_file' => $proofPath,
                'additional_notes' => $request->additional_notes ?? null,
                'metadata' => json_encode([
                    'verified_by' => request()->user()->full_name
                ]),
                'status' => 'paid'
            ]);

            // Generate receipt
            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'REC' . uniqid(),
                'amount' => $request->amount,
                'date' => now(),
                'is_installment' => false,
                'total_amount' => $invoice->amount,
                'remaining_amount' => 0,
                'payment_status' => 'paid'
            ]);

            // Update invoice status
            $invoice->update(['status' => 'paid']);

            // Log the activity
            activity()
                ->performedOn($payment)
                ->causedBy(request()->user())
                ->withProperties([
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $request->amount,
                    'payment_id' => $payment->id
                ])
                ->log('Manual payment verified');

            // Send notifications
            $invoice->student->user->notify(new StudentManualPaymentVerificationNotice($payment, 'paid'));

            User::admins()
                ->whereHas('admin', function ($query) {
                    $query->where('role', 'superAdmin');
                })
                ->each(function ($admin) use ($payment) {
                    $admin->notify(new AdminManualPaymentVerificationNotice($payment, 'paid'));
                });

            DB::commit();

            return redirect()->route('admin.payments.showReceipt', $receipt->id)
                ->with('success', 'Payment verified successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual payment verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }


    public function showConfirmationProve($paymentId)
    {
        $payment = Payment::with('invoice')->findOrFail($paymentId);

        return view('admin.payments.proof_of_payment', compact('payment'));
    }

    public function destroy($invoice)
    {

        $invoice =  Invoice::findOrFail($invoice)->first();
        //    dd($invoice);
        return redirect()->back()->with('success', 'Invoice delete successfully!');
    }
}
