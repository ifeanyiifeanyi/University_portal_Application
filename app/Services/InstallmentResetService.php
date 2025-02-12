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
        if (!$invoice->is_installment) {
            return false;
        }

        return DB::transaction(function () use ($invoice) {
            $originalData = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->amount
            ];

            // Handle payment and installments deletion if exists
            $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();
            if ($payment) {
                $originalData['payment_data'] = $payment->toArray();
                $originalData['installments'] = $payment->installments->toArray();

                $payment->installments()->delete();
                $payment->delete();
            }

            $invoice->forceDelete();

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->performedOn($invoice)
                ->withProperties([
                    'original_data' => $originalData,
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'deleted_at' => now()
                ])
                ->log('Payment method reset - all records deleted');

            return true;
        });
    }

    public function resetPayment(Invoice $invoice)
    {
        if ($invoice->is_installment) {
            throw new \Exception('Use InstallmentResetService for installment payments');
        }

        return DB::transaction(function () use ($invoice) {
            // Store original data for logging
            $originalData = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->amount
            ];

            // Delete associated payment if exists
            $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();
            if ($payment) {
                $originalData['payment_data'] = $payment->toArray();
                $payment->delete();
            }

            // Delete related records (e.g., proof of payments)
            // $invoice->proveOfPayment()->delete();

            // Delete the invoice
            $invoice->forceDelete();

            // Log the deletion
            activity()
                ->causedBy(auth()->user())
                ->performedOn($invoice)
                ->withProperties([
                    'original_data' => $originalData,
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'deleted_at' => now()
                ])
                ->log('Payment completely deleted');

            return true;
        });
    }
}
