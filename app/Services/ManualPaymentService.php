<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\PaymentProof;
use App\Models\ProveOfPayment;
use App\Models\PaymentInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ManualPaymentSubmitted;
use App\Notifications\PaymentVerificationResult;

class ManualPaymentService
{


    public function processManualPayment(array $data, $proofFile)
    {
        DB::beginTransaction();
        try {
            // Check for existing payment
            $this->validateNoExistingPayment($data['invoice']);

            // Create payment record
            $payment = $this->createPaymentRecord($data['invoice'], $data);

            // Handle proof file upload
            $proofPath = $this->handleFileUpload($proofFile);

            // Update payment with proof
            $payment->payment_proof = $proofPath;
            $payment->save();

            // Create payment proof record
            $this->createPaymentProof($payment, $data, $proofPath);

            // Create installments if this is an installment payment
            if (isset($data['is_installment']) && $data['is_installment']) {
                $this->createInstallments($payment, $data);
            }


            // Generate receipt
            $receipt = $this->generateReceipt($payment);

            // Update invoice status
            $this->updateInvoiceStatus($data['invoice'], $payment->status);

            $this->sendVerificationNotifications($payment, $payment->status);

            // Log the activity
            $this->logPaymentActivity($payment);

            // Send notification
            $this->sendPaymentNotification($payment);

            DB::commit();

            return [
                'payment' => $payment,
                'receipt' => $receipt
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate that no payment exists for this combination
     */
    private function validateNoExistingPayment($invoice)
    {
        $existingPayment = Payment::where([
            'student_id' => $invoice->student_id,
            'payment_type_id' => $invoice->payment_type_id,
            'academic_session_id' => $invoice->academic_session_id,
            'semester_id' => $invoice->semester_id,
        ])->whereIn('status', ['paid', 'partial'])->first();

        if ($existingPayment) {
            throw new \Exception('Payment already exists for this student and payment type.');
        }
    }

    /**
     * Create the payment record
     */

    private function createPaymentRecord($invoice, $data)
    {
        $isInstallment = $data['is_installment'] ?? false;
        $baseAmount = $data['base_amount'] ?? $invoice->amount;

        $paymentData = [
            'invoice_number' => $invoice->invoice_number,
            'student_id' => $invoice->student_id,
            'payment_type_id' => $invoice->payment_type_id,
            'payment_method_id' => $invoice->payment_method_id,
            'academic_session_id' => $invoice->academic_session_id,
            'semester_id' => $invoice->semester_id,
            'department_id' => $invoice->department_id,
            'level' => $invoice->level,
            'payment_channel' => 'MANUAL',
            'admin_id' => auth()->id(),
            'is_manual' => true,
            'invoice_id' => $invoice->id,
            'payment_date' => now(),
            'transaction_reference' => 'PAY' . uniqid() . 'MANUAL',
            'payment_reference' => $data['transaction_reference'] ?? null,
            'base_amount' => $baseAmount,
            'amount' => $invoice->amount,
            'late_fee' => $data['late_fee'] ?? 0,
            'is_installment' => $isInstallment,
            'installment_config_id' => $data['installment_config_id'] ?? null,
        ];

        if ($isInstallment) {
            $paymentData['status'] = 'partial';
            $paymentData['installment_status'] = 'partial';
            $paymentData['remaining_amount'] = $invoice->amount - $baseAmount;
            $paymentData['next_transaction_amount'] = $data['next_transaction_amount'] ?? $paymentData['remaining_amount'];
            $paymentData['next_installment_date'] = $data['next_installment_date'] ?? now()->addDays(30);
        } else {
            $paymentData['status'] = 'paid';
            $paymentData['installment_status'] = 'completed';
            $paymentData['remaining_amount'] = 0;
            $paymentData['next_transaction_amount'] = 0;
        }

        return Payment::create($paymentData);
    }


    private function generateReceipt(Payment $payment)
    {
        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC' . uniqid(),
            'amount' => $payment->base_amount,
            'date' => now(),
            'is_installment' => $payment->is_installment,
            'installment_number' => $payment->is_installment ? 1 : null,
            'total_amount' => $payment->amount,
            'remaining_amount' => $payment->remaining_amount,
        ]);
    }

    private function handleVerifiedPayment($payment, $adminComment)
    {
        if ($payment->is_installment) {
            $payment->status = 'partial';
            // Update the current installment status
            $payment->installments()
                ->where('installment_number', 1)
                ->update(['status' => 'paid', 'paid_at' => now()]);
        } else {
            $payment->status = 'paid';
        }

        $payment->admin_comment = $adminComment;
        $payment->save();

        // Update invoice status
        $invoice = Invoice::where('invoice_number', $payment->invoice_number)->first();
        if ($invoice) {
            $invoice->status = $payment->status;
            $invoice->save();
        }
    }


    /**
     * Handle file upload
     */
    private function handleFileUpload($file)
    {
        return $file->store('payment_proofs', 'public');
    }


    private function createPaymentProof($payment, $data, $proofPath)
    {
        return ProveOfPayment::create([
            'invoice_id' => $data['invoice']->id,
            'payment_type_id' => $data['invoice']->payment_type_id,
            'payment_method_id' => $data['invoice']->payment_method_id,
            'amount' => $payment->base_amount,
            'transaction_reference' => $data['transaction_reference'],
            'bank_name' => $data['bank_name'],
            'proof_file' => $proofPath,
            'additional_notes' => $data['additional_notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'status' => 'paid'
        ]);
    }


    private function createInstallments(Payment $payment, array $data)
    {
        // Create first installment for the amount being paid now
        PaymentInstallment::create([
            'payment_id' => $payment->id,
            'amount' => $data['amount_paid'],
            'paid_amount' => $data['amount_paid'],
            'installment_number' => 1,
            'due_date' => now(),
            'paid_at' => now(),
            'status' => 'paid'
        ]);

        // Create second installment for the remaining amount
        $remainingAmount = $payment->amount - $data['amount_paid'];
        PaymentInstallment::create([
            'payment_id' => $payment->id,
            'amount' => $remainingAmount,
            'paid_amount' => 0,
            'installment_number' => 2,
            'due_date' => now()->addDays($data['installment_due_days'] ?? 30),
            'status' => 'pending'
        ]);
    }
    /**
     * Send notifications for payment submission
     */
    // private function sendSubmissionNotifications($payment)
    // {
    //     // Notify student
    //     $payment->student->user->notify(new ManualPaymentSubmitted($payment));

    //     // Notify admins
    //     $admins = User::whereHas('admin', function ($query) {
    //         $query->whereIn('role', [Admin::TYPE_SUPER_ADMIN, Admin::TYPE_STAFF]);
    //     })->get();

    //     Notification::send($admins, new ManualPaymentSubmitted($payment));
    // }

    /**
     * Verify a manual payment
     */
    // TODO to be removed ***
    public function verifyPayment($payment, array $data)
    {
        DB::beginTransaction();
        try {
            $paymentProof = $payment->paymentProof;
            $paymentProof->status = $data['status'];
            $paymentProof->verified_by = auth()->id();
            $paymentProof->verified_at = now();
            $paymentProof->save();

            $receipt = null;
            if ($data['status'] === 'verified') {
                $this->handleVerifiedPayment($payment, $data['admin_comment']);
                $receipt = $this->generateReceipt($payment);
            }

            // Log the verification
            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'status' => $data['status'],
                    'admin_comment' => $data['admin_comment'],
                    'receipt_number' => $receipt ? $receipt->receipt_number : null
                ])
                ->log('Manual payment ' . $data['status']);

            // Send notification
            $this->sendVerificationNotifications($payment, $data['status']);

            DB::commit();

            return [
                'payment' => $payment,
                'receipt' => $receipt,
                'status' => $data['status']
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update invoice status
     */
    private function updateInvoiceStatus($invoice, $status)
    {
        $invoice->status = $status;
        $invoice->save();
    }
    /**
     * Log payment activity
     */
    private function logPaymentActivity($payment)
    {
        activity()
            ->performedOn($payment)
            ->causedBy(auth()->user())
            ->withProperties([
                'invoice_number' => $payment->invoice_number,
                'amount' => $payment->amount_paid,
                'is_installment' => $payment->is_installment
            ])
            ->log('Manual payment processed');
    }

    /**
     * Send payment notification
     */
    private function sendPaymentNotification($payment)
    {
        $payment->student->user->notify(new ManualPaymentSubmitted($payment));
    }





    /**
     * Send notifications for verification result
     */
    private function sendVerificationNotifications($payment, $status)
    {
        // Notify student
        $payment->student->user->notify(new PaymentVerificationResult($payment, $status));

        // Notify admin who submitted the payment
        $submittingAdmin = User::find($payment->admin_id);
        if ($submittingAdmin) {
            $submittingAdmin->notify(new PaymentVerificationResult($payment, $status));
        }
    }
}
