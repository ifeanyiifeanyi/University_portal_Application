<?php

namespace App\Services;


use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentReconciliationService
{
    protected $paystackSecretKey;
    protected $paymentGatewayService;

    public function __construct(PaymentGatewayService $paymentGatewayService)
    {
        $this->paystackSecretKey = env('PAYSTACK_SECRET_KEY');
        $this->paymentGatewayService = $paymentGatewayService;
    }

    public function reconcilePayments()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->get('https://api.paystack.co/transaction', [
                'status' => 'success',
                'perPage' => 100,
                'from' => now()->subDays(7)->format('Y-m-d'),
                'to' => now()->format('Y-m-d')
            ]);

            if ($response->successful()) {
                Log::info("responds OK");
            }

            // Log::info('Fetched Paystack transactions', [
            //     'response' => $response->json()['data']
            // ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch Paystack transactions: ' . ($response->json()['message'] ?? 'Unknown error'));
            }

            $paystackTransactions = collect($response->json()['data']);
            // Log::info('COLLECTION', [" PAYSTAXK" => $paystackTransactions]);

            // Get all pending payments from our database
            $pendingPayments = Payment::where('status', 'pending')
                ->where('created_at', '>=', now()->subDays(40))
                ->where('transaction_reference', '!=', '')
                ->get();

            Log::info("pending local ", ['local' => $pendingPayments]);

            $reconciliationResults = [
                'total_processed' => 0,
                'successful_updates' => 0,
                'failed_updates' => 0,
                'skipped' => 0
            ];

            foreach ($pendingPayments as $payment) {
                $reconciliationResults['total_processed']++;

                try {

                    // Add these debug logs right before the firstWhere:
                    Log::info('Searching for reference:', ['reference' => $payment->transaction_reference]);
                    Log::info('Available references:', [
                        'references' => $paystackTransactions->pluck('reference')->toArray()
                    ]);


                    // Find matching Paystack transaction
                    $paystackTransaction = $paystackTransactions->first(function ($transaction) use ($payment) {
                        return strtolower($transaction['reference']) === strtolower($payment->transaction_reference);
                    });
                    Log::info('payment tras', ['PAY' => $paystackTransaction]);

                    if (!$paystackTransaction) {
                        $reconciliationResults['skipped']++;
                        continue;
                    }

                    // Check if payment was successful in Paystack
                    if (strtolower($paystackTransaction['status']) === 'success') {
                        // Convert amount from kobo to Naira
                        $paidAmount = $paystackTransaction['amount'] / 100;

                        if ($payment->is_installment) {
                            $this->handleInstallmentReconciliation($payment, $paidAmount, $paystackTransaction);
                        } else {
                            $this->handleFullPaymentReconciliation($payment, $paidAmount, $paystackTransaction);
                        }

                        // Create receipt
                        $this->createReceipt($payment, $paidAmount);

                        $reconciliationResults['successful_updates']++;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to reconcile payment', [
                        'payment_id' => $payment->id,
                        'reference' => $payment->transaction_reference,
                        'error' => $e->getMessage()
                    ]);
                    $reconciliationResults['failed_updates']++;
                }
            }

            return $reconciliationResults;
        } catch (\Exception $e) {
            Log::error('Payment reconciliation process failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function handleInstallmentReconciliation($payment, $paidAmount, $paystackTransaction)
    {
        // Get current installment
        $currentInstallment = $payment->installments()
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();

        if (!$currentInstallment) {
            throw new \Exception('No pending installment found');
        }

        // Calculate penalty if applicable
        $penaltyAmount = 0;
        if ($currentInstallment->due_date->isPast()) {
            $penaltyAmount = $currentInstallment->calculatePenalty();
        }

        // Update current installment
        $currentInstallment->update([
            'paid_amount' => $payment->next_transaction_amount,
            'status' => 'paid',
            'paid_at' => Carbon::parse($paystackTransaction['paid_at'])
        ]);

        // Calculate new totals
        $totalPaid = $payment->installments()
            ->where('status', 'paid')
            ->sum('paid_amount');

        $remainingAmount = $payment->amount - $totalPaid;
        $nextInstallment = $payment->installments()
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();

        // Update payment record
        $install =  $payment->update([
            'base_amount' => $totalPaid,
            'remaining_amount' => $remainingAmount,
            'next_transaction_amount' => $nextInstallment ? $nextInstallment->amount : 0,
            'installment_status' => $nextInstallment ? 'partial' : 'completed',
            'next_installment_date' => $nextInstallment ? $nextInstallment->due_date : null,
            'payment_reference' => $paystackTransaction['reference'],
            'gateway_response' => json_encode($paystackTransaction),
            'payment_channel' => $paystackTransaction['channel'] ?? 'unknown',
            'status' => $nextInstallment ? 'partial' : 'paid',
            'admin_comment' => 'Payment reconciled for installment ' . $currentInstallment->installment_number
        ]);
        Log::info('INSTALLMENT', ['INSTAL' => $install]);

        // Update invoice status
        $this->updateInvoiceStatus($payment, $nextInstallment ? 'partial' : 'paid');
    }

    private function handleFullPaymentReconciliation($payment, $paidAmount, $paystackTransaction)
    {
        $pay = $payment->update([
            'base_amount' => $payment->amount,
            'payment_reference' => $paystackTransaction['reference'],
            'gateway_response' => json_encode($paystackTransaction),
            'payment_channel' => $paystackTransaction['channel'] ?? 'unknown',
            'status' => 'paid'
        ]);
        Log::info("GO HERE", ['PAY' => $pay]);

        // Update invoice status
        $this->updateInvoiceStatus($payment, 'paid');
    }

    private function updateInvoiceStatus($payment, $status)
    {
        Invoice::where([
            'student_id' => $payment->student_id,
            'payment_type_id' => $payment->payment_type_id,
            'academic_session_id' => $payment->academic_session_id,
            'semester_id' => $payment->semester_id,
        ])->update(['status' => $status]);
    }

    private function createReceipt($payment, $paidAmount)
    {
        // Check if receipt already exists
        $existingReceipt = Receipt::where('payment_id', $payment->id)->first();
        if ($existingReceipt) {
            return $existingReceipt;
        }

        return Receipt::create([
            'payment_id' => $payment->id,
            'student_id' => $payment->student_id,
            'receipt_number' => 'REC' . uniqid(),
            'amount' => $payment->base_amount,
            'date' => now(),
            'is_installment' => $payment->is_installment,
            'installment_number' => $payment->is_installment ?
                $payment->installments()->where('status', 'paid')->count() : null,
            'total_amount' => $payment->amount, // Full payment amount
            'remaining_amount' => $payment->is_installment ?
                $payment->remaining_amount : 0,
            'academic_session_id' => $payment->academic_session_id,
            'semester_id' => $payment->semester_id,
            'payment_type_id' => $payment->payment_type_id
        ]);
    }
}
