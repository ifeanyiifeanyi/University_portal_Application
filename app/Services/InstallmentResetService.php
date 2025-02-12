<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class InstallmentResetService
{
    /**
     * Reset the installment for a given invoice.
     *
     * @param Invoice $invoice
     * @return bool
     * @throws \Exception
     */
    public function resetInstallment(Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {
            // Check if the invoice is an installment
            if (!$invoice->is_installment) {
                throw new \Exception('Not an installment invoice');
            }

            // Find the associated payment
            $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();

            if (!$payment) {
                throw new \Exception('No payment record found');
            }

            // Store data for audit log
            $originalData = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->amount,
                'payment_data' => $payment->toArray(),
                'installments' => $payment->installments->toArray()
            ];

            // Delete payment installments
            $payment->installments()->delete();

            // Delete payment record
            $payment->delete();

            // Reset invoice
            $invoice->forceDelete();

            // Log the activity using Spatie Activitylog
            activity()
                ->causedBy(auth()->user()) // The user who performed the action
                ->performedOn($invoice) // The subject (invoice) being acted upon
                ->withProperties([ // Additional properties
                    'original_data' => $originalData,
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'deleted_at' => now()
                ])
                ->log('Payment method reset - all records deleted'); // Description of the activity

            return true;
        });
    }
}
