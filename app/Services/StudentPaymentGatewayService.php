<?php

namespace App\Services;

use Exception;
use Yabacon\Paystack;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\RemitaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class StudentPaymentGatewayService
{
    protected $remitaService;
    protected $paystack;

    public function __construct(RemitaService $remitaService)
    {
        $this->remitaService = $remitaService;
        $this->paystack = new Paystack(env("PAYSTACK_SECRET_KEY"));
    }

    public function initializePayment(Payment $payment, $amount = null)
    {
        $gateway = $payment->paymentMethod->config['gateway'];

        switch ($gateway) {
            case 'paystack':
                return $this->initializePaystackPayment($payment, $amount);

            default:
                throw new \Exception("Unsupported payment gateway: {$gateway}");
        }
    }


    private function initializePaystackPayment(Payment $payment, $amount = null)
    {
        try {
            // Get base amount that should go to the school
            $baseAmount = ($amount ?? $payment->next_transaction_amount ?? $payment->amount);

            // Our platform fee that should go to main account
            $platformFee = 500;

            // Calculate Paystack fee
            $subtotalBeforePaystackFee = $baseAmount + $platformFee;
            $paystackFeePercentage = 0.015; // 1.5%
            $paystackFixedFee = 100; // NGN 100

            // Calculate actual Paystack fee
            $calculatedPaystackFee = ($subtotalBeforePaystackFee * $paystackFeePercentage) + $paystackFixedFee;
            $actualPaystackFee = $calculatedPaystackFee > 2000 ? 2000 : $calculatedPaystackFee;

            // Calculate final amount student will pay
            $finalAmount = $baseAmount + $platformFee + $actualPaystackFee;

            // Convert to kobo for Paystack
            $amountInKobo = ceil($finalAmount * 100);

            // Prepare transaction data
            $transactionData = [
                'amount' => $amountInKobo,
                'email' => $payment->student->user->email,
                'reference' => $payment->transaction_reference,
                'callback_url' => route('student.fees.payment.verify'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'base_amount' => $baseAmount,
                    'platform_fee' => $platformFee,
                    'paystack_fee' => $actualPaystackFee,
                ]
            ];

            // If payment type has a subaccount
            $paymentType = $payment->paymentType;
            if ($paymentType->paystack_subaccount_code) {
                $transactionData['subaccount'] = $paymentType->paystack_subaccount_code;
                $transactionData['transaction_charge'] = floor(($platformFee / $finalAmount) * 100);
            }

            // Initialize transaction
            $initializationResponse = $this->paystack->transaction->initialize($transactionData);

            if ($initializationResponse->status) {
                return $initializationResponse->data->authorization_url;
            }

            throw new \Exception('Failed to initialize payment: ' . $initializationResponse->message);
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paystack payment: ' . $e->getMessage());
        }
    }



    private function verifyPaystackPayment($reference)
    {
        try {
            // Verify transaction using Paystack package
            $transactionResponse = $this->paystack->transaction->verify([
                'reference' => $reference
            ]);

            // Check transaction status
            if (!$transactionResponse->status) {
                throw new \Exception('Transaction verification failed');
            }

            $data = $transactionResponse->data;
            $paidAmount = $data->amount / 100; // Convert back from kobo

            // Retrieve payment record
            $payment = Payment::where('transaction_reference', $reference)->firstOrFail();

            // Basic validation
            $successStatuses = ['success', 'completed'];
            if (!in_array(strtolower($data->status), $successStatuses)) {
                throw new \Exception('Payment not successful. Status: ' . $data->status);
            }

            // Retrieve payment breakdown or reconstruct if necessary
            $paymentBreakdown = $this->getPaymentBreakdownFromCache($reference)
                ?? $this->reconstructPaymentBreakdown($payment);

            // Validate payment amount
            $expectedBaseAmount = $payment->is_installment ? $payment->next_transaction_amount : $payment->amount;
            $expectedTotalAmount = $paymentBreakdown['final_amount'];
            $allowedDifference = 1;

            $this->validatePaymentAmount($expectedTotalAmount, $paidAmount, $allowedDifference);

            // Prepare response data for logging and return
            $responseData = [
                'status' => true,
                'data' => [
                    'status' => $data->status,
                    'reference' => $reference,
                    'amount' => $data->amount,
                    'channel' => $data->channel ?? 'unknown'
                ]
            ];

            // Process payment based on type
            if ($payment->is_installment) {
                $this->handleInstallmentPayment($payment, $expectedBaseAmount, $responseData);
            } else {
                $this->handleFullPayment($payment, $expectedBaseAmount, $responseData);
            }

            // Clear payment cache
            $this->clearPaymentCache($reference);

            return [
                'success' => true,
                'reference' => $reference,
                'amount' => $expectedBaseAmount,
                'total_paid' => $paidAmount,
                'is_installment' => $payment->is_installment,
                'remaining_amount' => $payment->is_installment ? $payment->remaining_amount : 0,
                'cache_source' => $paymentBreakdown['reconstructed'] ?? false ? 'reconstructed' : 'cache',
                'metadata' => [
                    'channel' => $data->channel ?? 'unknown',
                    'card_type' => $data->authorization->card_type ?? null,
                    'bank' => $data->authorization->bank ?? null,
                ]
            ];
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            $this->logPaymentError($e, ['reference' => $reference]);
            throw new \Exception('Paystack API verification failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logPaymentError($e, ['reference' => $reference]);
            throw $e;
        }
    }

    private function handleInstallmentPayment(Payment $payment, $paidAmount, $responseData)
    {
        $currentInstallment = $payment->installments()
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();

        if (!$currentInstallment) {
            throw new \Exception('No pending installment found');
        }

        $currentInstallment->update([
            'paid_amount' => $paidAmount,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        $totalPaid = $payment->installments()
            ->where('status', 'paid')
            ->sum('paid_amount');

        $nextInstallment = $payment->installments()
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();

        $updateData = [
            'base_amount' => $totalPaid,
            'payment_reference' => $responseData['data']['reference'],
            'gateway_response' => json_encode($responseData['data']),
            'payment_channel' => $responseData['data']['channel'] ?? 'unknown',
            'status' => $nextInstallment ? 'partial' : 'paid',
            'remaining_amount' => $nextInstallment ? ($payment->amount - $totalPaid) : 0,
            'next_transaction_amount' => $nextInstallment ? $nextInstallment->amount : 0,
            'next_installment_date' => $nextInstallment ? $nextInstallment->due_date : null,
            'admin_comment' => 'Payment received for installment ' . $currentInstallment->installment_number . ' of ' . $payment->installments()->count() . ' installments'
        ];

        $payment->update($updateData);

        Invoice::where([
            'student_id' => $payment->student_id,
            'payment_type_id' => $payment->payment_type_id,
            'academic_session_id' => $payment->academic_session_id,
            'semester_id' => $payment->semester_id,
        ])->update(['status' => $nextInstallment ? 'partial' : 'paid']);
    }

    private function handleFullPayment(Payment $payment, $paidAmount, $responseData)
    {
        $payment->update([
            'base_amount' => $paidAmount,
            'payment_reference' => $responseData['data']['reference'],
            'gateway_response' => json_encode($responseData['data']),
            'payment_channel' => $responseData['data']['channel'] ?? 'unknown',
            'status' => 'paid'
        ]);

        Invoice::where([
            'student_id' => $payment->student_id,
            'payment_type_id' => $payment->payment_type_id,
            'academic_session_id' => $payment->academic_session_id,
            'semester_id' => $payment->semester_id,
        ])->update(['status' => 'paid']);
    }
    private function calculatePaystackFee($amount)
    {
        $paystackFeePercentage = 0.015; // 1.5%
        $paystackFixedFee = 100; // NGN 100
        $calculatedFee = ($amount * $paystackFeePercentage) + $paystackFixedFee;
        return $calculatedFee > 2000 ? 2000 : $calculatedFee;
    }

    public function verifyPayment($gateway, $reference)
    {
        try {
            switch ($gateway) {
                case 'paystack':
                    return $this->verifyPaystackPayment($reference);

                default:
                    throw new \Exception("Unsupported payment gateway: {$gateway}");
            }
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'gateway' => $gateway,
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function clearPaymentCache($reference)
    {
        try {
            $cacheKey = "payment_breakdown_{$reference}";
            cache()->forget($cacheKey);
            cache()->forget("backup_{$cacheKey}");

            Log::info('Payment cache cleared successfully', [
                'reference' => $reference
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to clear payment cache', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function validatePaystackResponse($responseData)
    {
        if (!isset($responseData['data'])) {
            throw new \Exception('Invalid response structure: missing data object');
        }

        if (!isset($responseData['data']['status'])) {
            throw new \Exception('Invalid response structure: missing status');
        }

        if (!isset($responseData['data']['amount'])) {
            throw new \Exception('Invalid response structure: missing amount');
        }

        return true;
    }

    private function validatePaymentAmount($expectedAmount, $receivedAmount, $allowedDifference = 1)
    {
        $difference = abs($expectedAmount - $receivedAmount);

        if ($difference > $allowedDifference) {
            throw new \Exception(sprintf(
                'Payment amount mismatch. Expected: %s, Received: %s, Difference: %s',
                $expectedAmount,
                $receivedAmount,
                $difference
            ));
        }

        return true;
    }

    private function getPaymentBreakdownFromCache($reference)
    {
        // Try primary cache
        $cacheKey = "payment_breakdown_{$reference}";
        $breakdown = cache()->get($cacheKey);

        if ($breakdown) {
            Log::info('Payment breakdown retrieved from primary cache', [
                'reference' => $reference
            ]);
            return $breakdown;
        }

        // Try backup cache
        $breakdown = cache()->get("backup_{$cacheKey}");

        if ($breakdown) {
            Log::info('Payment breakdown retrieved from backup cache', [
                'reference' => $reference
            ]);
            return $breakdown;
        }

        Log::warning('Payment breakdown not found in cache', [
            'reference' => $reference
        ]);

        return null;
    }

    private function reconstructPaymentBreakdown(Payment $payment)
    {
        $baseAmount = $payment->is_installment ? $payment->next_transaction_amount : $payment->amount;
        $platformFee = 500;
        $paystackFee = $this->calculatePaystackFee($baseAmount + $platformFee);

        $breakdown = [
            'base_amount' => $baseAmount,
            'platform_fee' => $platformFee,
            'paystack_fee' => $paystackFee,
            'final_amount' => $baseAmount + $platformFee + $paystackFee,
            'amount_in_kobo' => ceil(($baseAmount + $platformFee + $paystackFee) * 100),
            'reconstructed' => true,
            'reconstructed_at' => now()->timestamp,
            'payment_id' => $payment->id,
            'student_id' => $payment->student_id
        ];

        Log::info('Payment breakdown reconstructed', [
            'reference' => $payment->transaction_reference,
            'breakdown' => $breakdown
        ]);

        return $breakdown;
    }

    private function logPaymentError(\Exception $e, $context = [])
    {
        $logContext = array_merge([
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'timestamp' => now()->toDateTimeString()
        ], $context);

        Log::error('Payment processing error', $logContext);
    }




    /**
     * Handle Paystack Webhook
     *
     * @param array $payload Webhook payload
     * @param string $sigHeader Signature header
     * @return array
     */
    public function handlePaystackWebhook($payload, $sigHeader)
    {
        try {
            // Ensure payload is a string
            $payloadString = is_array($payload) ? json_encode($payload) : $payload;

            // Verify webhook signature
            $this->verifyWebhookSignature($payloadString, $sigHeader);

            // Parse webhook payload
            $event = json_decode($payloadString, true);

            // Log the entire webhook for debugging
            Log::info('Paystack Webhook Received', [
                'event' => $event,
                'type' => $event['event'] ?? 'unknown'
            ]);

            // Handle different webhook events
            switch ($event['event'] ?? null) {
                case 'charge.success':
                    return $this->handleChargeSuccessWebhook($event);

                case 'transfer.success':
                    return $this->handleTransferSuccessWebhook($event);

                case 'invoice.payment_succeeded':
                    return $this->handleInvoicePaymentWebhook($event);

                default:
                    Log::warning('Unhandled Paystack Webhook Event', [
                        'event' => $event['event'] ?? 'unknown'
                    ]);
                    return ['status' => 'ignored', 'message' => 'Unhandled event type'];
            }
        } catch (Exception $e) {
            $this->logWebhookError($e, $payloadString);
            throw $e;
        }
    }

    /**
     * Verify Webhook Signature
     *
     * @param string $payload Webhook payload
     * @param string $sigHeader Signature header
     * @throws Exception
     */


    private function verifyWebhookSignature($payload, $sigHeader)
    {
        try {
            $verified = $this->paystack->webhook->verifySignature(
                $payload,
                $sigHeader,
                env('PAYSTACK_SECRET_KEY')
            );

            if (!$verified) {
                throw new Exception('Webhook signature verification failed');
            }
        } catch (Exception $e) {
            Log::error('Webhook Signature Verification Failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle Charge Success Webhook
     *
     * @param array $event Webhook event data
     * @return array
     */
    private function handleChargeSuccessWebhook($event)
    {
        try {
            // Extract transaction details
            $transactionData = $event['data'] ?? [];
            $reference = $transactionData['reference'] ?? null;

            if (!$reference) {
                throw new Exception('No transaction reference found in webhook');
            }

            // Find the corresponding payment
            $payment = Payment::where('transaction_reference', $reference)->first();

            if (!$payment) {
                Log::warning('Payment not found for webhook', [
                    'reference' => $reference
                ]);
                return ['status' => 'ignored', 'message' => 'Payment not found'];
            }

            // Verify the payment amount and process
            $paidAmount = $transactionData['amount'] / 100; // Convert from kobo
            $expectedAmount = $payment->is_installment
                ? $payment->next_transaction_amount
                : $payment->amount;

            // Process payment
            $this->processWebhookPayment($payment, $paidAmount, $transactionData);

            return [
                'status' => 'success',
                'message' => 'Payment processed via webhook',
                'payment_id' => $payment->id
            ];
        } catch (Exception $e) {
            Log::error('Charge Success Webhook Processing Failed', [
                'error' => $e->getMessage(),
                'event' => $event
            ]);
            throw $e;
        }
    }


    /**
     * Process Payment from Webhook
     *
     * @param Payment $payment Payment model
     * @param float $paidAmount Paid amount
     * @param array $transactionData Transaction details
     */
    private function processWebhookPayment($payment, $paidAmount, $transactionData)
    {
        DB::beginTransaction();
        try {
            // Prepare response data structure similar to verification method
            $responseData = [
                'status' => true,
                'data' => [
                    'status' => $transactionData['status'] ?? 'success',
                    'reference' => $transactionData['reference'],
                    'amount' => $transactionData['amount'],
                    'channel' => $transactionData['channel'] ?? 'unknown'
                ]
            ];

            // Process based on payment type
            if ($payment->is_installment) {
                $this->handleInstallmentPayment($payment, $paidAmount, $responseData);
            } else {
                $this->handleFullPayment($payment, $paidAmount, $responseData);
            }

            // Clear payment cache
            $this->clearPaymentCache($payment->transaction_reference);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Webhook Payment Processing Failed', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id
            ]);
            throw $e;
        }
    }

    /**
     * Handle Transfer Success Webhook
     *
     * @param array $event Webhook event data
     * @return array
     */
    private function handleTransferSuccessWebhook($event)
    {
        // Implement transfer-specific logic if needed
        Log::info('Transfer Success Webhook Received', [
            'transfer_data' => $event['data'] ?? []
        ]);

        return [
            'status' => 'success',
            'message' => 'Transfer webhook processed'
        ];
    }

    /**
     * Handle Invoice Payment Webhook
     *
     * @param array $event Webhook event data
     * @return array
     */
    private function handleInvoicePaymentWebhook($event)
    {
        // Implement invoice-specific logic if needed
        Log::info('Invoice Payment Webhook Received', [
            'invoice_data' => $event['data'] ?? []
        ]);

        return [
            'status' => 'success',
            'message' => 'Invoice payment webhook processed'
        ];
    }

    /**
     * Log Webhook Error
     *
     * @param Exception $e Exception
     * @param string $payload Webhook payload
     */
    private function logWebhookError(Exception $e, $payload)
    {
        Log::error('Paystack Webhook Error', [
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'payload' => $payload,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Webhook Retry Mechanism
     *
     * @param string $reference Transaction reference
     * @param int $maxRetries Maximum retry attempts
     */
    public function webhookPaymentRetry($reference, $maxRetries = 3)
    {
        $attempts = cache()->get("webhook_retry_{$reference}", 0);

        if ($attempts >= $maxRetries) {
            Log::error('Max webhook retry attempts reached', [
                'reference' => $reference,
                'max_attempts' => $maxRetries
            ]);
            return false;
        }

        try {
            // Attempt to verify payment
            $verificationResult = $this->verifyPaystackPayment($reference);

            // Clear retry cache if successful
            cache()->forget("webhook_retry_{$reference}");

            return $verificationResult;
        } catch (Exception $e) {
            // Increment retry attempts
            cache()->put(
                "webhook_retry_{$reference}",
                $attempts + 1,
                now()->addHours(24)
            );

            Log::warning('Webhook retry attempt failed', [
                'reference' => $reference,
                'attempt' => $attempts + 1,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
