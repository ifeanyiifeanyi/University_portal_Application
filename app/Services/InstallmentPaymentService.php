<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ProveOfPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InstallmentPaymentService
{
    /**
     * Calculate installment details based on payment history
     */
    public function calculateInstallmentDetails(Invoice $invoice)
    {
        // Get existing payments for this invoice
        $existingPayments = Payment::where('invoice_id', $invoice->id)
            ->where('status', '!=', 'failed')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalPaid = $existingPayments->sum('base_amount');
        $remainingAmount = $invoice->amount - $totalPaid;
        $installmentNumber = $existingPayments->count() + 1;

        return [
            'total_paid' => $totalPaid,
            'remaining_amount' => $remainingAmount,
            'default_amount' => $remainingAmount,
            'next_due_date' => $this->calculateNextDueDate($existingPayments),
            'installment_number' => $installmentNumber,
            'is_final_installment' => $remainingAmount <= ($invoice->amount * 0.25) // Assuming last payment should be at least 25%
        ];
    }

    /**
     * Calculate the next due date based on payment history
     */
    private function calculateNextDueDate($existingPayments)
    {
        if ($existingPayments->isEmpty()) {
            // First installment - due date is 30 days from now
            return now()->addDays(30);
        }

        $lastPayment = $existingPayments->last();
        $lastDueDate = $lastPayment->next_installment_date ?? $lastPayment->created_at;

        // If the last due date has passed, set next due date to 30 days from now
        if ($lastDueDate->isPast()) {
            return now()->addDays(30);
        }

        // Otherwise, add 30 days to the last due date
        return $lastDueDate->addDays(30);
    }

    /**
     * Process an installment payment
     */
    public function processInstallment(Invoice $invoice, array $data, $proofFile)
    {
        DB::beginTransaction();
        try {
            $installmentDetails = $this->calculateInstallmentDetails($invoice);

            // Validate payment amount
            if ($data['base_amount'] > $installmentDetails['remaining_amount']) {
                throw new \Exception('Payment amount cannot exceed remaining balance.');
            }

            // Create payment record with installment details
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
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
                'invoice_number' => $invoice->invoice_number,
                'transaction_reference' => 'PAY' . uniqid() . 'MANUAL',
                'payment_reference' => $data['transaction_reference'],
                'base_amount' => $data['base_amount'],
                'amount' => $invoice->amount,
                'is_installment' => true,
                'installment_number' => $installmentDetails['installment_number'],
                'remaining_amount' => $installmentDetails['remaining_amount'] - $data['base_amount'],
                'next_transaction_amount' => $installmentDetails['is_final_installment'] ? 0 :
                    ($installmentDetails['remaining_amount'] - $data['base_amount']),
                'next_installment_date' => $installmentDetails['is_final_installment'] ? null :
                    $installmentDetails['next_due_date'],
                'status' => $installmentDetails['is_final_installment'] ? 'paid' : 'partial',
                'installment_status' => $installmentDetails['is_final_installment'] ? 'completed' : 'partial',
                'payment_date' => now()
            ]);

            // Create payment proof record
            $proofPath = Storage::disk('public')->put('payment_proofs', $proofFile);
            ProveOfPayment::create([
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'payment_type_id' => $invoice->payment_type_id,
                'payment_method_id' => $invoice->payment_method_id,
                'amount' => $data['base_amount'],
                'transaction_reference' => $data['transaction_reference'],
                'bank_name' => $data['bank_name'],
                'proof_file' => $proofPath,
                'additional_notes' => $data['additional_notes'] ?? null,
                'status' => 'pending'
            ]);

            // Update invoice status
            $invoice->update([
                'status' => $installmentDetails['is_final_installment'] ? 'paid' : 'partial'
            ]);

            DB::commit();
            return $payment;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
