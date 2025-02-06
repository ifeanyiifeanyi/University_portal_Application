<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\RemitaService;
use Illuminate\Support\Facades\Log;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Facades\Http;
use App\Models\Invoice;

class StudentPaymentGatewayService
{
    protected $remitaService;
    protected $paystackSecretKey;

    public function __construct(RemitaService $remitaService)
    {
        $this->remitaService = $remitaService;
        $this->paystackSecretKey = env("PAYSTACK_SECRET_KEY");
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
            if (empty($this->paystackSecretKey)) {
                throw new \Exception('Paystack configuration is missing');
            }

            $paymentType = $payment->paymentType;

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

            // Only apply 2000 cap for transactions where calculated fee would exceed 2000
            $actualPaystackFee = $calculatedPaystackFee > 2000 ? 2000 : $calculatedPaystackFee;

            // Calculate final amount student will pay
            $finalAmount = $baseAmount + $platformFee + $actualPaystackFee;

            // Convert to kobo for Paystack
            $amountInKobo = ceil($finalAmount * 100);

            // Calculate split percentages
            $schoolPercentage = ($baseAmount / $finalAmount) * 100;
            $platformPercentage = ($platformFee / $finalAmount) * 100;

            // Create payment breakdown for caching
            $paymentBreakdown = [
                'base_amount' => $baseAmount,
                'platform_fee' => $platformFee,
                'paystack_fee' => $actualPaystackFee,
                'final_amount' => $finalAmount,
                'amount_in_kobo' => $amountInKobo,
                'initialized_at' => now()->timestamp,
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'school_percentage' => $schoolPercentage,
                'platform_percentage' => $platformPercentage,
                'verification' => [
                    'school_will_receive' => $baseAmount,
                    'platform_will_receive' => $platformFee,
                    'paystack_will_receive' => $actualPaystackFee,
                    'customer_will_pay' => $finalAmount
                ]
            ];

            Log::info('Payment Calculation Details', $paymentBreakdown);

            // Store in primary cache
            $cacheKey = "payment_breakdown_{$payment->transaction_reference}";
            cache()->remember($cacheKey, now()->addHours(24), function () use ($paymentBreakdown) {
                return $paymentBreakdown;
            });

            // Store in backup cache with longer duration
            cache()->remember("backup_{$cacheKey}", now()->addHours(48), function () use ($paymentBreakdown) {
                return $paymentBreakdown;
            });

            $paymentData = [
                'amount' => $amountInKobo,
                'first_name' => $payment->student->user->first_name,
                'last_name' => $payment->student->user->last_name,
                'email' => $payment->student->user->email,
                'phone' => $payment->student->user->phone,
                'reference' => $payment->transaction_reference,
                'callback_url' => route('student.fees.payment.verify', ['gateway' => 'paystack']),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'payment_type' => $paymentType->name,
                    'is_installment' => $payment->is_installment,
                    'installment_number' => $payment->is_installment ? 1 : null,
                    'base_amount' => $baseAmount,
                    'platform_fee' => $platformFee,
                    'paystack_fee' => $actualPaystackFee,
                    'total_amount' => $finalAmount
                ]
            ];

            if ($paymentType->paystack_subaccount_code) {
                $paymentData['split'] = [
                    'type' => 'percentage',
                    'bearer_type' => 'account',
                    'subaccounts' => [
                        [
                            'subaccount' => $paymentType->paystack_subaccount_code,
                            'share' => floor($schoolPercentage)
                        ]
                    ]
                ];

                Log::info('Split Payment Configuration', [
                    'total_amount' => $finalAmount,
                    'school_amount' => $baseAmount,
                    'platform_fee' => $platformFee,
                    'paystack_fee' => $actualPaystackFee,
                    'school_percentage' => floor($schoolPercentage),
                    'platform_percentage' => floor($platformPercentage)
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($this->paystackSecretKey),
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', $paymentData);

            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['status']) {
                    return $responseData['data']['authorization_url'];
                }
            }

            // Clear caches if initialization fails
            cache()->forget($cacheKey);
            cache()->forget("backup_{$cacheKey}");

            throw new \Exception('Failed to initialize payment: ' . ($response->json()['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paystack payment: ' . $e->getMessage());
        }
    }

    private function verifyPaystackPayment($reference)
    {
        try {
            Log::info('Initiating Paystack payment verification', ['reference' => $reference]);

            $payment = Payment::where('transaction_reference', $reference)->firstOrFail();

            // Try to get payment breakdown from primary cache
            $cacheKey = "payment_breakdown_{$reference}";
            $paymentBreakdown = cache()->get($cacheKey);

            // If primary cache fails, try backup cache
            if (!$paymentBreakdown) {
                Log::warning('Primary cache miss, attempting backup cache', ['reference' => $reference]);
                $paymentBreakdown = cache()->get("backup_{$cacheKey}");
            }

            // If both caches fail, reconstruct from payment record
            if (!$paymentBreakdown) {
                Log::warning('Cache miss for payment breakdown, reconstructing from payment record', [
                    'reference' => $reference,
                    'payment_id' => $payment->id
                ]);

                $baseAmount = $payment->is_installment ? $payment->next_transaction_amount : $payment->amount;
                $platformFee = 500;
                $paystackFee = $this->calculatePaystackFee($baseAmount + $platformFee);

                $paymentBreakdown = [
                    'base_amount' => $baseAmount,
                    'platform_fee' => $platformFee,
                    'paystack_fee' => $paystackFee,
                    'final_amount' => $baseAmount + $platformFee + $paystackFee,
                    'reconstructed' => true
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($this->paystackSecretKey),
                'Content-Type' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$response->successful()) {
                throw new \Exception('Paystack API request failed: ' . ($response->json()['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();
            Log::info(['payment response' => $responseData]);

            if (!isset($responseData['data']) || !isset($responseData['data']['status'])) {
                throw new \Exception('Invalid response structure from Paystack');
            }

            $successStatuses = ['success', 'completed'];
            if (!$responseData['status'] || !in_array(strtolower($responseData['data']['status']), $successStatuses)) {
                throw new \Exception('Payment not successful. Status: ' . ($responseData['data']['status'] ?? 'unknown'));
            }

            $paidAmount = $responseData['data']['amount'] / 100;
            $expectedBaseAmount = $payment->is_installment ? $payment->next_transaction_amount : $payment->amount;

            // Verify payment amount
            if ($paymentBreakdown) {
                $expectedTotalAmount = $paymentBreakdown['final_amount'];
                $difference = abs($paidAmount - $expectedTotalAmount);
                $allowedDifference = 1;

                if ($difference > $allowedDifference) {
                    throw new \Exception(sprintf(
                        'Payment amount mismatch. Expected: %s, Received: %s',
                        $expectedTotalAmount,
                        $paidAmount
                    ));
                }
            } else {
                $minimumExpectedTotal = $expectedBaseAmount + 500;
                if ($paidAmount < $minimumExpectedTotal) {
                    throw new \Exception(sprintf(
                        'Payment amount too low. Minimum expected: %s, Received: %s',
                        $minimumExpectedTotal,
                        $paidAmount
                    ));
                }
            }

            // Process payment based on type
            if ($payment->is_installment) {
                $this->handleInstallmentPayment($payment, $expectedBaseAmount, $responseData);
            } else {
                $this->handleFullPayment($payment, $expectedBaseAmount, $responseData);
            }

            // Clear caches after successful verification
            cache()->forget($cacheKey);
            cache()->forget("backup_{$cacheKey}");

            return [
                'success' => true,
                'reference' => $reference,
                'amount' => $expectedBaseAmount,
                'total_paid' => $paidAmount,
                'is_installment' => $payment->is_installment,
                'remaining_amount' => $payment->is_installment ? $payment->remaining_amount : 0,
                'cache_source' => $paymentBreakdown['reconstructed'] ?? false ? 'reconstructed' : 'cache',
                'metadata' => [
                    'channel' => $responseData['data']['channel'] ?? 'unknown',
                    'card_type' => $responseData['data']['authorization']['card_type'] ?? null,
                    'bank' => $responseData['data']['authorization']['bank'] ?? null,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'cache_status' => isset($paymentBreakdown) ? 'available' : 'missing'
            ]);
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
}
