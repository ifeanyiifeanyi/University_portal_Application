<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\PaymentInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;

class InstallmentPaymentController extends Controller
{
    protected $paymentGatewayService;
    public function __construct(PaymentGatewayService $paymentGatewayService)
    {
        $this->paymentGatewayService = $paymentGatewayService;
    }

    public function showNextInstallmentDetails(PaymentInstallment $installment)
    {
        $installment->load([
            'payment.student.user',
            'payment.student.department',
            'payment.paymentType',
            'payment.paymentMethod',
            'payment.academicSession',
            'payment.semester'
        ]);

        $lateFee = $installment->calculatePenalty();
        $totalAmount = $installment->amount + $lateFee;

        return view('admin.payments.installments.next-payment', compact(
            'installment',
            'lateFee',
            'totalAmount'
        ));
    }

    /***
     *
     * TODO: each installment payment should have its own receipt and invoice
     * TODO: add a section for only pending installments, pay from there too
     * TODO: add link to student profile from the tables
     */
    public function processNextInstallment(PaymentInstallment $installment)
    {
        DB::beginTransaction();

        try {
            // Validate installment is ready for payment
            if (!in_array($installment->status, ['pending', 'overdue'])) {
                throw new \Exception('This installment is not available for payment.');
            }

            $payment = $installment->payment;

            // Verify payment is still active and in installment mode
            if (!$payment->is_installment || $payment->installment_status === 'completed') {
                throw new \Exception('Invalid payment status for installment processing.');
            }

            // Calculate late fees if applicable
            $lateFee = $installment->calculatePenalty();
            $totalAmount = $installment->amount + $lateFee;

            // Find the next pending installment after this one
            $nextInstallment = $payment->installments()
                ->where('status', 'pending')
                ->where('installment_number', '>', $installment->installment_number)
                ->orderBy('installment_number')
                ->first();

            // Update the current payment record with new transaction details
            $payment->update([
                'transaction_reference' => 'PAY' . uniqid(),
                'next_transaction_amount' => $totalAmount,
                'next_installment_date' => $nextInstallment ? $nextInstallment->due_date : null,
                'status' => 'pending',
                'payment_date' => now(),
                'admin_comment' => 'Processing installment ' . $installment->installment_number . ' of ' .
                    $payment->installments()->count()
            ]);

            // Create or update invoice for this installment
            $invoice = Invoice::updateOrCreate(
                [
                    'student_id' => $payment->student_id,
                    'payment_type_id' => $payment->payment_type_id,
                    'department_id' => $payment->department_id,
                    'level' => $payment->level,
                    'academic_session_id' => $payment->academic_session_id,
                    'semester_id' => $payment->semester_id,
                ],
                [
                    'invoice_number' => 'INV' . uniqid(),
                    'amount' => $totalAmount,
                    'payment_method_id' => $payment->payment_method_id,
                    'status' => 'pending',
                    'is_installment' => true,
                ]
            );

            // Calculate the total paid amount and remaining amount
            $totalPaidSoFar = $payment->installments()
                ->where('status', 'paid')
                ->sum('paid_amount');

            $remainingAmount = $payment->amount - ($totalPaidSoFar + $totalAmount);

            // Update payment with the remaining amount
            $payment->update([
                'remaining_amount' => $remainingAmount
            ]);

            // Initialize payment with gateway
            $paymentUrl = $this->paymentGatewayService->initializePayment(
                $payment,
                $totalAmount
            );

            DB::commit();
            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Next installment processing failed', [
                'payment_id' => $payment->id ?? null,
                'installment_id' => $installment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to process installment payment: ' . $e->getMessage());
        }
    }
}
