<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\PaymentProof;
use App\Models\ProveOfPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ManualPaymentSubmitted;
use App\Notifications\PaymentVerificationResult;

class ManualPaymentService
{
    /**
     * Process a manual payment submission
     */
    // public function processManualPayment(array $data, $proofFile)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // Check for existing payment
    //         $this->validateNoExistingPayment($data['invoice']);

    //         // Create or update payment record
    //         $payment = $this->createPaymentRecord($data['invoice']);

    //         // Handle proof file upload
    //         $proofPath = $this->handleFileUpload($proofFile);

    //         // Create payment proof record
    //         $paymentProof = $this->createPaymentProof($payment, $data, $proofPath);

    //         // Log the activity
    //         activity()
    //             ->performedOn($payment)
    //             ->causedBy(auth()->user())
    //             ->withProperties([
    //                 'invoice_number' => $data['invoice']->invoice_number,
    //                 'amount' => $data['invoice']->amount,
    //                 'payment_proof_id' => $paymentProof->id
    //             ])
    //             ->log('Manual payment proof submitted');

    //         // Send notifications
    //         $this->sendSubmissionNotifications($payment);

    //         DB::commit();
    //         return $payment;

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    public function processManualPayment(array $data, $proofFile)
    {
        DB::beginTransaction();
        try {
            // Check for existing payment
            $this->validateNoExistingPayment($data['invoice']);

            // Create or update payment record
            $payment = $this->createPaymentRecord($data['invoice']);

            // Handle proof file upload
            $proofPath = $this->handleFileUpload($proofFile);

            // Create payment proof record
            $paymentProof = $this->createPaymentProof($payment, $data, $proofPath);

            // Log the activity
            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'invoice_number' => $data['invoice']->invoice_number,
                    'amount' => $data['invoice']->amount,
                    'payment_proof_id' => $paymentProof->id
                ])
                ->log('Manual payment proof submitted');

            // Send notifications
            $this->sendSubmissionNotifications($payment);

            DB::commit();
            return $payment;
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
        ])->where('status', 'paid')->first();

        if ($existingPayment) {
            throw new \Exception('Payment already exists for this student and payment type.');
        }
    }

    /**
     * Create the payment record
     */
    private function createPaymentRecord($invoice)
    {
        return Payment::create([
            'invoice_number' => $invoice->invoice_number,
            'student_id' => $invoice->student_id,
            'payment_type_id' => $invoice->payment_type_id,
            'payment_method_id' => $invoice->payment_method_id,
            'academic_session_id' => $invoice->academic_session_id,
            'semester_id' => $invoice->semester_id,
            'amount' => $invoice->amount,
            'department_id' => $invoice->department_id,
            'level' => $invoice->level,
            'status' => 'pending',
            'admin_id' => auth()->id(),
            'is_manual' => true,
            'payment_date' => now(),
            'transaction_reference' => 'PAY' . uniqid() . 'MANUAL',
        ]);
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
            'amount' => $data['invoice']->amount,
            'transaction_reference' => $data['transaction_reference'],
            'bank_name' => $data['bank_name'],
            'proof_file' => $proofPath,
            'additional_notes' => $data['additional_notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'status' => 'pending'
        ]);
    }

    /**
     * Send notifications for payment submission
     */
    private function sendSubmissionNotifications($payment)
    {
        // Notify student
        $payment->student->user->notify(new ManualPaymentSubmitted($payment));

        // Notify admins
        $admins = User::whereHas('admin', function ($query) {
            $query->whereIn('role', [Admin::TYPE_SUPER_ADMIN, Admin::TYPE_STAFF]);
        })->get();

        Notification::send($admins, new ManualPaymentSubmitted($payment));
    }

    /**
     * Verify a manual payment
     */
    // public function verifyPayment($payment, array $data)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $paymentProof = $payment->paymentProof;
    //         $paymentProof->status = $data['status'];
    //         $paymentProof->verified_by = auth()->id();
    //         $paymentProof->verified_at = now();
    //         $paymentProof->save();

    //         if ($data['status'] === 'verified') {
    //             $this->handleVerifiedPayment($payment, $data['admin_comment']);
    //         }

    //         // Log the verification
    //         activity()
    //             ->performedOn($payment)
    //             ->causedBy(auth()->user())
    //             ->withProperties([
    //                 'status' => $data['status'],
    //                 'admin_comment' => $data['admin_comment']
    //             ])
    //             ->log('Manual payment ' . $data['status']);

    //         // Send notification
    //         $this->sendVerificationNotifications($payment, $data['status']);

    //         DB::commit();
    //         return true;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

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
     * Generate receipt for verified payment
     */
    protected function generateReceipt(Payment $payment)
    {
        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC' . uniqid(),
            'amount' => $payment->amount,
            'date' => now(),
        ]);
    }


    /**
     * Handle verified payment updates
     */
    private function handleVerifiedPayment($payment, $adminComment)
    {
        $payment->status = 'paid';
        $payment->admin_comment = $adminComment;
        $payment->save();

        // Update invoice status
        $invoice = Invoice::where('invoice_number', $payment->invoice_number)->first();
        if ($invoice) {
            $invoice->status = 'paid';
            $invoice->save();
        }
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
