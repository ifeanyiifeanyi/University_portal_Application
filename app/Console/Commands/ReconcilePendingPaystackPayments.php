<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\ReconcilePendingPayments;
use App\Services\PaymentReconciliationService;

class ReconcilePendingPaystackPayments extends Command
{
    protected $signature = 'payments:reconcile';
    protected $description = 'Reconcile pending payments with Paystack transactions';

    protected $reconciliationService;

    public function __construct(PaymentReconciliationService $reconciliationService)
    {
        parent::__construct();
        $this->reconciliationService = $reconciliationService;
    }


    public function handle()
    {
        try {
            $this->info('Starting payment reconciliation process...');

            $results = $this->reconciliationService->reconcilePayments();

            $this->info('Reconciliation completed:');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Processed', $results['total_processed']],
                    ['Successfully Updated', $results['successful_updates']],
                    ['Failed Updates', $results['failed_updates']],
                    ['Skipped', $results['skipped']]
                ]
            );

            return 0;
        } catch (\Exception $e) {
            Log::error('Payment reconciliation failed', [
                'error' => $e->getMessage()
            ]);
            $this->error('Failed to reconcile payments: ' . $e->getMessage());
            return 1;
        }
    }

    // public function handle()
    // {
    //     try {
    //         // Get all pending payments from last 7 days
    //         $pendingPayments = Payment::where('status', 'pending')
    //             ->where('created_at', '>=', now()->subDays(7))
    //             ->where('transaction_reference', '!=', '')
    //             ->chunk(10, function ($payments) {
    //                 foreach ($payments as $payment) {
    //                     // Dispatch job for each pending payment
    //                     ReconcilePendingPayments::dispatch($payment)
    //                         ->onQueue('payment-reconciliation');

    //                     $this->info("Queued reconciliation for payment: {$payment->transaction_reference}");
    //                 }
    //             });

    //         $this->info('Payment reconciliation jobs have been queued successfully');
    //         return 0;
    //     } catch (\Exception $e) {
    //         Log::error('Failed to queue payment reconciliation jobs', [
    //             'error' => $e->getMessage()
    //         ]);
    //         $this->error('Failed to queue payment reconciliation jobs: ' . $e->getMessage());
    //         return 1;
    //     }
    // }
}
