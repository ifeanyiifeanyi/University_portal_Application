<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\PaymentReconciliationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ReconcilePendingPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $maxExceptions = 3;
    public $backoff = [60, 180, 300]; // Retry after 1 minute, 3 minutes, then 5 minutes

    protected $payment;

    
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function handle(PaymentReconciliationService $reconciliationService)
    {
        try {
            Log::info('Starting individual payment reconciliation', [
                'reference' => $this->payment->transaction_reference,
                'payment_id' => $this->payment->id
            ]);

            // Verify single payment status with Paystack
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Content-Type' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$this->payment->transaction_reference}");

            if (!$response->successful()) {
                Log::error('Failed to verify payment with Paystack', [
                    'reference' => $this->payment->transaction_reference,
                    'response' => $response->json()
                ]);
                return;
            }

            $paystackTransaction = $response->json()['data'];

            // Check if payment was successful in Paystack
            if (strtolower($paystackTransaction['status']) === 'success') {
                $paidAmount = $paystackTransaction['amount'] / 100;

                // Use the reconciliation service methods
                if ($this->payment->is_installment) {
                    $reconciliationService->handleInstallmentReconciliation(
                        $this->payment,
                        $paidAmount,
                        $paystackTransaction
                    );
                } else {
                    $reconciliationService->handleFullPaymentReconciliation(
                        $this->payment,
                        $paidAmount,
                        $paystackTransaction
                    );
                }

                // Create receipt
                $reconciliationService->createReceipt($this->payment, $paidAmount);

                Log::info('Successfully reconciled individual payment', [
                    'reference' => $this->payment->transaction_reference,
                    'amount' => $paidAmount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Individual payment reconciliation failed', [
                'reference' => $this->payment->transaction_reference,
                'error' => $e->getMessage()
            ]);

            // Determine if we should retry
            if ($this->attempts() < $this->tries) {
                throw $e; // This will trigger a retry
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Payment reconciliation job failed permanently', [
            'payment_id' => $this->payment->id,
            'reference' => $this->payment->transaction_reference,
            'error' => $exception->getMessage()
        ]);

        // You could add notification logic here
        // For example, notify admin about permanently failed reconciliation
    }
}
