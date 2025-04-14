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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\PaymentInstallmentConfig;
use App\Notifications\AdminManualPaymentVerificationNotice;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ManualPaymentSubmitted;
use App\Notifications\PaymentVerificationResult;
use App\Notifications\StudentManualPaymentVerificationNotice;

class ManualPaymentService
{

    public function processManualPayment(array $data, $proofFile, $totalAmount)
    {
        DB::beginTransaction();
        try {
             // Check for existing payment
             $this->validateNoExistingPayment($data['invoice']);

            // Find the existing payment record for this invoice
            $payment = Payment::where([
                'student_id' => $data['invoice']->student_id,
                'payment_type_id' => $data['invoice']->payment_type_id,
                'academic_session_id' => $data['invoice']->academic_session_id,
                'semester_id' => $data['invoice']->semester_id,
                'invoice_number' => $data['invoice']->invoice_number,
            ])->firstOrFail();

            // Get payment type configuration for installments
            $paymentTypeConfig = PaymentInstallmentConfig::where('payment_type_id', $data['invoice']->payment_type_id)
                ->where('is_active', true)
                ->first();

            // Determine if this is an installment payment
            $isInstallment = isset($data['is_installment']) && $data['is_installment'];

            // Get base amount
            $baseAmount = $isInstallment ? $data['base_amount'] : $totalAmount;

            // Validate installment amount if applicable
            if ($isInstallment && !$paymentTypeConfig) {
                throw new \Exception('No active installment configuration found for this payment type');
            }

            if ($isInstallment) {
                $this->validateInstallmentAmount($baseAmount, $totalAmount);
            }

            // Get or set default admin comment
            $adminComment = $data['additional_notes'] ?? 'Payment processed by ' . auth()->user()->full_name;

            // Update payment record with manual payment details
            $payment->update([
                'payment_channel' => 'MANUAL',
                'admin_id' => auth()->id(),
                'admin_comment' => $adminComment,
                'is_manual' => true,
                'payment_date' => now(),
                'transaction_reference' => 'PAY' . uniqid() . 'MANUAL',
                'payment_reference' => $data['transaction_reference'] ?? null,
                'base_amount' => $baseAmount,
                'late_fee' => $data['late_fee'] ?? 0,
                'is_installment' => $isInstallment
            ]);

            // Handle installment-specific updates
            if ($isInstallment) {
                $remainingAmount = $data['invoice']->amount - $baseAmount;
                $payment->update([
                    'status' => 'partial',
                    'installment_status' => 'partial',
                    'remaining_amount' => $remainingAmount,
                    'next_transaction_amount' => $baseAmount,
                    'next_installment_date' => $data['next_installment_date']
                ]);
            } else {
                $payment->update([
                    'status' => 'paid',
                    'installment_status' => 'completed',
                    'remaining_amount' => 0,
                    'next_transaction_amount' => 0
                ]);
            }

            // Handle proof file upload
            $proofPath = $this->handleFileUpload($proofFile);

            // Update payment with proof
            $payment->payment_proof = $proofPath;
            $payment->save();

            // Create proof of payment record
            ProveOfPayment::create([
                'invoice_id' => $data['invoice']->id,
                'payment_type_id' => $payment->payment_type_id,
                'payment_method_id' => $payment->payment_method_id,
                'amount' => $payment->base_amount,
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'proof_file' => $proofPath,
                'additional_notes' => $data['additional_notes'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => $payment->status
            ]);

            // Handle installment setup if applicable
            if ($isInstallment) {
                $this->setupInstallmentPayments($payment, $data, $totalAmount, $baseAmount, $paymentTypeConfig);
            }

            // Generate receipt
            $receipt = $this->generateReceipt($payment);

            // Update invoice status
            $this->updateInvoiceStatus($data['invoice'], $payment->status);

            // Send notifications
            $this->sendVerificationNotifications($payment, $payment->status);

            // Log the activity
            $this->logPaymentActivity($payment);

            DB::commit();

            return [
                'payment' => $payment,
                'receipt' => $receipt
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }




    public function validateInstallmentAmount($baseAmount, $totalAmount)
    {
        // if ($baseAmount >= $totalAmount) {
        //     throw new \Exception('Installment amount cannot be greater than or equal to total amount');
        // }

        if ($baseAmount <= 0) {
            throw new \Exception('Installment amount must be greater than zero');
        }
    }

    /**
     * Validate that no payment exists for this combination
     */
    public function validateNoExistingPayment($invoice)
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

    public function createPaymentRecord($invoice, $data, $baseAmount, $adminComment)
    {
        $isInstallment = $data['is_installment'] ?? false;

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
            'admin_comment' => $adminComment,
            'is_manual' => true,
            'invoice_id' => $invoice->id,
            'payment_date' => now(),
            'transaction_reference' => 'PAY' . uniqid() . 'MANUAL',
            'payment_reference' => $data['transaction_reference'] ?? null,
            'base_amount' => $baseAmount,
            'amount' => $invoice->amount,
            'late_fee' => $data['late_fee'] ?? 0,
            'is_installment' => $isInstallment
        ];

        if ($isInstallment) {
            $remainingAmount = $invoice->amount - $baseAmount;
            $paymentData = array_merge($paymentData, [
                'status' => 'partial',
                'installment_status' => 'partial',
                'remaining_amount' => $remainingAmount,
                'next_transaction_amount' => $baseAmount,
                'next_installment_date' => $data['next_installment_date']
            ]);
        } else {
            $paymentData = array_merge($paymentData, [
                'status' => 'paid',
                'installment_status' => 'completed',
                'remaining_amount' => 0,
                'next_transaction_amount' => 0
            ]);
        }

        return Payment::create($paymentData);
    }


    public function generateReceipt(Payment $payment)
    {
        // dd("generateReceipt");
        // Get current installment number if this is an installment payment
        $installmentNumber = null;
        if ($payment->is_installment) {
            $installmentNumber = $payment->installments()
                ->where('status', 'paid')
                ->count();
        }

        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC' . uniqid(),
            'amount' => $payment->base_amount,
            'date' => now(),
            'is_installment' => $payment->is_installment,
            'installment_number' => $installmentNumber,
            'total_amount' => $payment->amount,
            'remaining_amount' => $payment->remaining_amount,
            'payment_status' => $payment->status // Add payment status to receipt
        ]);
    }


    public function setupInstallmentPayments(Payment $payment, array $data, $totalAmount, $firstPaymentAmount, $installmentConfig)
    {
        // dd($installmentConfig, $totalAmount, $firstPaymentAmount);
        // Ensure installment config is not a string
        if (is_string($installmentConfig)) {
            $installmentConfig = PaymentInstallmentConfig::where('id', $installmentConfig)->first();
        }

        // Validate installment config exists
        if (!$installmentConfig) {
            throw new \Exception('No valid installment configuration found');
        }

        // Calculate remaining installment details
        $remainingAmount = $totalAmount - $firstPaymentAmount;
        $remainingInstallments = (int) $installmentConfig->number_of_installments - 1;
        $nextInstallmentAmount = round($remainingAmount / $remainingInstallments, 2);

        // Create first installment record
        PaymentInstallment::create([
            'payment_id' => $payment->id,
            'installment_number' => 1,
            'amount' => $firstPaymentAmount,
            'paid_amount' => $firstPaymentAmount,
            'due_date' => now(),
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // Create subsequent installment records
        $nextDueDate = now();
        for ($i = 2; $i <= $installmentConfig->number_of_installments; $i++) {
            $nextDueDate = $nextDueDate->copy()->addDays($installmentConfig->interval_days);

            // Adjust last installment amount to match exact total
            $installmentAmount = ($i == $installmentConfig->number_of_installments)
                ? $remainingAmount
                : $nextInstallmentAmount;

            PaymentInstallment::create([
                'payment_id' => $payment->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'paid_amount' => 0,
                'due_date' => $nextDueDate,
                'status' => 'pending',
                'paid_at' => null
            ]);
        }

        // Find next pending installment
        $nextPendingInstallment = PaymentInstallment::where('payment_id', $payment->id)
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();

        // Update payment with installment tracking
        $payment->update([
            'amount' => $totalAmount, // Total amount from payment type
            'base_amount' => $firstPaymentAmount, // Current paid amount
            'payment_installment_configs_id' => $installmentConfig->id,
            'next_installment_date' => $nextPendingInstallment->due_date,
            'remaining_amount' => $remainingAmount,
            'next_transaction_amount' => $nextPendingInstallment->amount,
            'installment_status' => 'partial',
            'status' => 'partial'
        ]);

        return $payment;
    }
    public function handleVerifiedPayment($payment, $adminComment)
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
    public function handleFileUpload($file)
    {
        return $file->store('payment_proofs', 'public');
    }



   

    /**
     * Update invoice status
     */
    public function updateInvoiceStatus($invoice, $status)
    {
        $invoice->status = $status;
        $invoice->save();
    }
    /**
     * Log payment activity
     */
    // Add proper error messages for debugging
    public function logPaymentActivity($payment)
    {
        try {
            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'invoice_number' => $payment->invoice_number,
                    'amount' => $payment->base_amount,
                    'is_installment' => $payment->is_installment,
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ])
                ->log('Manual payment processed');
        } catch (\Exception $e) {
            Log::error('Failed to log payment activity', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }


    /**
     * Send notifications for verification result
     */
    public function sendVerificationNotifications($payment, $status)
    {
        // Notify student
        $payment->student->user->notify(new StudentManualPaymentVerificationNotice($payment, $status));



        // Notify all superadmins
        User::admins()
            ->whereHas('admin', function ($query) {
                $query->where('role', 'superAdmin');
            })
            ->each(function ($admin) use ($payment, $status) {
                $admin->notify(new AdminManualPaymentVerificationNotice($payment, $status));
            });
    }
}
