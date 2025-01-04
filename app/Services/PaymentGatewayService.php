<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\RemitaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentInstallmentConfig;
use Unicodeveloper\Paystack\Facades\Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class PaymentGatewayService
{
    protected $remitaService;
    protected $paystackSecretKey;

    public function __construct(RemitaService $remitaService)
    {
        $this->remitaService = $remitaService;
        // $this->paystackSecretKey = config('services.paystack.secret_key');
        $this->paystackSecretKey = env("PAYSTACK_SECRET_KEY");
    }


    public function initializePayment(Payment $payment, $amount = null)
    {
        $gateway = $payment->paymentMethod->config['gateway'];

        switch ($gateway) {
            case 'paystack':
                return $this->initializePaystackPayment($payment, $amount);
            case 'remita':
                return $this->initializeRemitaPayment($payment, $amount);
            default:
                throw new \Exception("Unsupported payment gateway: {$gateway}");
        }
    }


    // private function initializePaystackPayment(Payment $payment, $amount = null)
    // {
    //     try {
    //         if (empty($this->paystackSecretKey)) {
    //             throw new \Exception('Paystack configuration is missing');
    //         }


    //         $paymentType = $payment->paymentType;

    //         // Get base amount that should go to the school
    //         $baseAmount = ($amount ?? $payment->next_transaction_amount ?? $payment->amount);

    //         // Our platform fee that should go to main account
    //         $platformFee = 500;

    //         // Calculate Paystack fee
    //         $subtotalBeforePaystackFee = $baseAmount + $platformFee;
    //         $paystackFeePercentage = 0.015; // 1.5%
    //         $paystackFixedFee = 100; // NGN 100

    //         // Calculate potential Paystack fee
    //         $potentialPaystackFee = ($amountToCharge + $platformFee) * $paystackFeePercentage + $paystackFixedFee;

    //         // Apply fee cap of 2000 NGN
    //         $actualPaystackFee = min($potentialPaystackFee, 2000);

    //         // Calculate final amount
    //         $finalAmount = $amountToCharge + $platformFee + $actualPaystackFee;

    //         // Convert to kobo for Paystack
    //         $amountInKobo = $finalAmount * 100;

    //         // Calculate subaccount percentage to ensure school gets exact amount
    //         $subaccountPercentage = ($amountToCharge / $finalAmount) * 100;

    //         Log::info('Payment Calculation Details', [
    //             'base_amount' => $amountToCharge,
    //             'platform_fee' => $platformFee,
    //             'potential_paystack_fee' => $potentialPaystackFee,
    //             'actual_paystack_fee' => $actualPaystackFee,
    //             'final_amount' => $finalAmount,
    //             'amount_in_kobo' => $amountInKobo,
    //             'subaccount_percentage' => $subaccountPercentage,
    //             'verification' => [
    //                 'school_will_receive' => $amountToCharge,
    //                 'platform_will_receive' => $platformFee,
    //                 'paystack_will_receive' => $actualPaystackFee,
    //                 'customer_will_pay' => $finalAmount
    //             ]
    //         ]);

    //         $paymentData = [
    //             'amount' => $amountInKobo,
    //             'first_name' => $payment->student->user->first_name,
    //             'last_name' => $payment->student->user->last_name,
    //             'email' => $payment->student->user->email,
    //             'phone' => $payment->student->user->phone,
    //             'reference' => $payment->transaction_reference,
    //             'callback_url' => route('payment.verify', ['gateway' => 'paystack']),
    //             'metadata' => [
    //                 'payment_id' => $payment->id,
    //                 'student_id' => $payment->student_id,
    //                 'payment_type' => $paymentType->name,
    //                 'is_installment' => $payment->is_installment,
    //                 'installment_number' => $payment->is_installment ? 1 : null,
    //                 'base_amount' => $amountToCharge,
    //                 'platform_fee' => $platformFee,
    //                 'paystack_fee' => $actualPaystackFee,
    //                 'total_amount' => $finalAmount
    //             ]
    //         ];

    //         // Handle subaccount configuration
    //         if ($paymentType->paystack_subaccount_code) {
    //             $paymentData['split'] = [
    //                 'type' => 'percentage',
    //                 'bearer_type' => 'account',
    //                 'subaccounts' => [
    //                     [
    //                         'subaccount' => $paymentType->paystack_subaccount_code,
    //                         'share' => floor($subaccountPercentage)
    //                     ]
    //                 ]
    //             ];

    //             Log::info('Split Payment Configuration', [
    //                 'total_amount' => $finalAmount,
    //                 'base_amount' => $amountToCharge,
    //                 'platform_fee' => $platformFee,
    //                 'paystack_fee' => $actualPaystackFee,
    //                 'subaccount_percentage' => floor($subaccountPercentage)
    //             ]);
    //         }

    //         // Store calculation details for verification during callback
    //         cache()->put(
    //             "payment_breakdown_{$payment->transaction_reference}",
    //             [
    //                 'base_amount' => $amountToCharge,
    //                 'platform_fee' => $platformFee,
    //                 'paystack_fee' => $actualPaystackFee,
    //                 'final_amount' => $finalAmount
    //             ],
    //             now()->addHours(24)
    //         );

    //         // Make the API request
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . trim($this->paystackSecretKey),
    //             'Content-Type' => 'application/json',
    //         ])->post('https://api.paystack.co/transaction/initialize', $paymentData);

    //         if ($response->successful()) {
    //             $responseData = $response->json();
    //             if ($responseData['status']) {
    //                 return $responseData['data']['authorization_url'];
    //             }
    //         }

    //         throw new \Exception('Failed to initialize payment: ' . ($response->json()['message'] ?? 'Unknown error'));
    //     } catch (\Exception $e) {
    //         Log::error('Paystack payment initialization error: ' . $e->getMessage());
    //         throw new \Exception('Failed to initialize Paystack payment: ' . $e->getMessage());
    //     }
    // }


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
            // The school should get their exact amount as a percentage of the total
            $schoolPercentage = ($baseAmount / $finalAmount) * 100;
            // Platform fee as percentage of total
            $platformPercentage = ($platformFee / $finalAmount) * 100;

            Log::info('Payment Calculation Details', [
                'base_amount' => $baseAmount,
                'platform_fee' => $platformFee,
                'calculated_paystack_fee' => $calculatedPaystackFee,
                'actual_paystack_fee' => $actualPaystackFee,
                'is_capped' => $calculatedPaystackFee > 2000,
                'final_amount' => $finalAmount,
                'amount_in_kobo' => $amountInKobo,
                'school_percentage' => $schoolPercentage,
                'platform_percentage' => $platformPercentage,
                'verification' => [
                    'school_will_receive' => $baseAmount,
                    'platform_will_receive' => $platformFee,
                    'paystack_will_receive' => $actualPaystackFee,
                    'customer_will_pay' => $finalAmount
                ]
            ]);

            $paymentData = [
                'amount' => $amountInKobo,
                'first_name' => $payment->student->user->first_name,
                'last_name' => $payment->student->user->last_name,
                'email' => $payment->student->user->email,
                'phone' => $payment->student->user->phone,
                'reference' => $payment->transaction_reference,
                'callback_url' => route('payment.verify', ['gateway' => 'paystack']),
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

            // Handle split payment configuration
            if ($paymentType->paystack_subaccount_code) {
                $paymentData['split'] = [
                    'type' => 'percentage',
                    'bearer_type' => 'account', // Specifies that the main account bears the transaction charge
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

            // Store calculation details for verification during callback
            cache()->put(
                "payment_breakdown_{$payment->transaction_reference}",
                [
                    'base_amount' => $baseAmount,
                    'platform_fee' => $platformFee,
                    'paystack_fee' => $actualPaystackFee,
                    'final_amount' => $finalAmount,
                    'amount_in_kobo' => $amountInKobo
                ],
                now()->addHours(24)
            );

            // Make the API request
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

            throw new \Exception('Failed to initialize payment: ' . ($response->json()['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paystack payment: ' . $e->getMessage());
        }
    }





    private function initializeRemitaPayment(Payment $payment)
    {
        $paymentDetails = [
            'studentName' => $payment->student->user->full_name,
            'studentEmail' => $payment->student->user->email,
            'studentPhone' => $payment->student->user->phone,
            'description' => $payment->paymentType->name,
            'amount' => $payment->amount,
            'studentId' => $payment->student->id,
            'feeType' => $payment->paymentType->name,
            'semester' => $payment->semester->name,
            'academicYear' => $payment->academicSession->name,
        ];

        $response = $this->remitaService->initializeStudentPayment($paymentDetails);

        if ($response['status'] === 'success') {
            return $response['data']['paymentUrl']; // Return only the URL
        } else {
            Log::error('Remita payment initialization failed: ' . ($response['message'] ?? 'Unknown error'));
            throw new \Exception('Failed to initialize Remita payment');
        }
    }



    public function verifyPayment($gateway, $reference)
    {
        switch ($gateway) {
            case 'paystack':
                return $this->verifyPaystackPayment($reference);
            case 'remita':
                return $this->verifyRemitaPayment($reference);
            default:
                throw new \Exception("Unsupported payment gateway: {$gateway}");
        }
    }




    // private function verifyPaystackPayment($reference)
    // {
    //     try {
    //         Log::info('Initiating Paystack payment verification', [
    //             'reference' => $reference
    //         ]);

    //         if (empty($this->paystackSecretKey)) {
    //             throw new \Exception('Paystack secret key is missing');
    //         }

    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . trim($this->paystackSecretKey),
    //             'Content-Type' => 'application/json',
    //         ])->get("https://api.paystack.co/transaction/verify/{$reference}");

    //         if (!$response->successful()) {
    //             throw new \Exception('Paystack API request failed: ' . ($response->json()['message'] ?? 'Unknown error'));
    //         }

    //         $responseData = $response->json();

    //         // Validate response structure
    //         if (!isset($responseData['data']) || !isset($responseData['data']['status'])) {
    //             throw new \Exception('Invalid response structure from Paystack');
    //         }

    //         $successStatuses = ['success', 'completed'];
    //         if (!$responseData['status'] || !in_array(strtolower($responseData['data']['status']), $successStatuses)) {
    //             throw new \Exception('Payment not successful. Status: ' . ($responseData['data']['status'] ?? 'unknown'));
    //         }

    //         // Find the payment record
    //         $payment = Payment::where('transaction_reference', $reference)->firstOrFail();

    //         // Get the cached payment breakdown
    //         $paymentBreakdown = cache()->get("payment_breakdown_{$reference}");

    //         if (!$paymentBreakdown) {
    //             Log::warning('Payment breakdown not found in cache', ['reference' => $reference]);
    //         }

    //         // Convert amount from kobo to Naira
    //         $paidAmount = $responseData['data']['amount'] / 100;

    //         // Get the expected base amount (without fees)
    //         $expectedBaseAmount = $payment->is_installment
    //             ? $payment->next_transaction_amount
    //             : $payment->amount;

    //         if ($paymentBreakdown) {
    //             // If we have the breakdown, verify the total amount matches
    //             $expectedTotalAmount = $paymentBreakdown['final_amount'];

    //             // Allow for a small difference in amount (e.g., 1 Naira)
    //             $difference = abs($paidAmount - $expectedTotalAmount);
    //             $allowedDifference = 1;

    //             if ($difference > $allowedDifference) {
    //                 throw new \Exception(sprintf(
    //                     'Total payment amount mismatch. Expected: %s, Received: %s',
    //                     $expectedTotalAmount,
    //                     $paidAmount
    //                 ));
    //             }

    //             // Log the successful verification
    //             Log::info('Payment verification successful', [
    //                 'reference' => $reference,
    //                 'expected_base_amount' => $expectedBaseAmount,
    //                 'expected_total_amount' => $expectedTotalAmount,
    //                 'received_amount' => $paidAmount,
    //                 'platform_fee' => $paymentBreakdown['platform_fee'],
    //                 'paystack_fee' => $paymentBreakdown['paystack_fee']
    //             ]);
    //         } else {
    //             // Fallback verification if breakdown is not available
    //             // Calculate the minimum expected total (base amount + minimum fees)
    //             $minimumExpectedTotal = $expectedBaseAmount + 500; // base + platform fee
    //             if ($paidAmount < $minimumExpectedTotal) {
    //                 throw new \Exception(sprintf(
    //                     'Payment amount too low. Minimum expected: %s, Received: %s',
    //                     $minimumExpectedTotal,
    //                     $paidAmount
    //                 ));
    //             }
    //         }

    //         // Handle installment payment updates
    //         if ($payment->is_installment) {
    //             $this->handleInstallmentPayment($payment, $expectedBaseAmount, $responseData);
    //         } else {
    //             $this->handleFullPayment($payment, $expectedBaseAmount, $responseData);
    //         }

    //         return [
    //             'success' => true,
    //             'reference' => $reference,
    //             'amount' => $expectedBaseAmount, // Return the base amount without fees
    //             'total_paid' => $paidAmount, // Include the total amount paid including fees
    //             'is_installment' => $payment->is_installment,
    //             'remaining_amount' => $payment->is_installment ? $payment->remaining_amount : 0,
    //             'metadata' => [
    //                 'channel' => $responseData['data']['channel'] ?? 'unknown',
    //                 'card_type' => $responseData['data']['authorization']['card_type'] ?? null,
    //                 'bank' => $responseData['data']['authorization']['bank'] ?? null,
    //             ]
    //         ];
    //     } catch (\Exception $e) {
    //         Log::error('Payment verification failed', [
    //             'reference' => $reference,
    //             'error' => $e->getMessage()
    //         ]);
    //         throw $e;
    //     }
    // }

    private function verifyPaystackPayment($reference)
    {
        try {
            Log::info('Initiating Paystack payment verification', [
                'reference' => $reference
            ]);

            if (empty($this->paystackSecretKey)) {
                throw new \Exception('Paystack secret key is missing');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($this->paystackSecretKey),
                'Content-Type' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$response->successful()) {
                throw new \Exception('Paystack API request failed: ' . ($response->json()['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();

            // Validate response structure
            if (!isset($responseData['data']) || !isset($responseData['data']['status'])) {
                throw new \Exception('Invalid response structure from Paystack');
            }

            $successStatuses = ['success', 'completed'];
            if (!$responseData['status'] || !in_array(strtolower($responseData['data']['status']), $successStatuses)) {
                throw new \Exception('Payment not successful. Status: ' . ($responseData['data']['status'] ?? 'unknown'));
            }

            // Find the payment record
            $payment = Payment::where('transaction_reference', $reference)->firstOrFail();

            // Get the cached payment breakdown
            $paymentBreakdown = cache()->get("payment_breakdown_{$reference}");

            if (!$paymentBreakdown) {
                Log::warning('Payment breakdown not found in cache', ['reference' => $reference]);
            }

            // Convert amount from kobo to Naira
            $paidAmount = $responseData['data']['amount'] / 100;

            // Get the expected base amount (without fees)
            $expectedBaseAmount = $payment->is_installment
                ? $payment->next_transaction_amount
                : $payment->amount;

            if ($paymentBreakdown) {
                // If we have the breakdown, verify the total amount matches
                $expectedTotalAmount = $paymentBreakdown['final_amount'];

                // Allow for a small difference in amount (e.g., 1 Naira)
                $difference = abs($paidAmount - $expectedTotalAmount);
                $allowedDifference = 1;

                if ($difference > $allowedDifference) {
                    throw new \Exception(sprintf(
                        'Total payment amount mismatch. Expected: %s, Received: %s',
                        $expectedTotalAmount,
                        $paidAmount
                    ));
                }

                // Log the successful verification
                Log::info('Payment verification successful', [
                    'reference' => $reference,
                    'expected_base_amount' => $expectedBaseAmount,
                    'expected_total_amount' => $expectedTotalAmount,
                    'received_amount' => $paidAmount,
                    'platform_fee' => $paymentBreakdown['platform_fee'],
                    'paystack_fee' => $paymentBreakdown['paystack_fee']
                ]);
            } else {
                // Fallback verification if breakdown is not available
                // Calculate the minimum expected total (base amount + minimum fees)
                $minimumExpectedTotal = $expectedBaseAmount + 500; // base + platform fee
                if ($paidAmount < $minimumExpectedTotal) {
                    throw new \Exception(sprintf(
                        'Payment amount too low. Minimum expected: %s, Received: %s',
                        $minimumExpectedTotal,
                        $paidAmount
                    ));
                }
            }

            // Handle installment payment updates
            if ($payment->is_installment) {
                $this->handleInstallmentPayment($payment, $expectedBaseAmount, $responseData);
            } else {
                $this->handleFullPayment($payment, $expectedBaseAmount, $responseData);
            }

            return [
                'success' => true,
                'reference' => $reference,
                'amount' => $expectedBaseAmount, // Return the base amount without fees
                'total_paid' => $paidAmount, // Include the total amount paid including fees
                'is_installment' => $payment->is_installment,
                'remaining_amount' => $payment->is_installment ? $payment->remaining_amount : 0,
                'metadata' => [
                    'channel' => $responseData['data']['channel'] ?? 'unknown',
                    'card_type' => $responseData['data']['authorization']['card_type'] ?? null,
                    'bank' => $responseData['data']['authorization']['bank'] ?? null,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }




    private function handleInstallmentPayment(Payment $payment, $paidAmount, $responseData)
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
            'paid_amount' => $paidAmount,
            'status' => 'paid',
            'paid_at' => now()
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
        $payment->update([
            'base_amount' => $totalPaid,
            'remaining_amount' => $remainingAmount,
            'next_transaction_amount' => $nextInstallment ? $nextInstallment->amount : null,
            'installment_status' => $nextInstallment ? 'partial' : 'completed',
            'next_installment_date' => $nextInstallment ? $nextInstallment->due_date : null,
            'payment_reference' => $responseData['data']['reference'],
            'gateway_response' => json_encode($responseData['data']),
            'payment_channel' => $responseData['data']['channel'] ?? 'unknown',
            'status' => $nextInstallment ? 'partial' : 'paid',
            'admin_comment' => 'Payment received for installment ' . $currentInstallment->installment_number . ' of ' . $payment->installments()->count() . ' installments',

            'admin_id' => auth()->user()->id
        ]);

        // Update invoice status based on payment completion
        $invoiceStatus = $nextInstallment ? 'partial' : 'paid';
        Invoice::where([
            'student_id' => $payment->student_id,
            'payment_type_id' => $payment->payment_type_id,
            'academic_session_id' => $payment->academic_session_id,
            'semester_id' => $payment->semester_id,
        ])->update(['status' => $invoiceStatus]);
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

        // Update the associated invoice status
        Invoice::where([
            'student_id' => $payment->student_id,
            'payment_type_id' => $payment->payment_type_id,
            'academic_session_id' => $payment->academic_session_id,
            'semester_id' => $payment->semester_id,
        ])->update(['status' => 'paid']);
    }

    private function verifyRemitaPayment($reference)
    {
        $response = $this->remitaService->verifyPayment($reference);

        if ($response['status'] === 'success' && $response['data']['paymentStatus'] === 'SUCCESS') {
            return [
                'success' => true,
                'reference' => $reference,
                'amount' => $response['data']['amount'],
            ];
        }

        return ['success' => false];
    }




    // TODO: This is incomplete Fetch subaccount transactions
    public function getSubaccountTransactionsPaystack($subaccountCode)
    {
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Accept' => 'application/json',
            ])->get('https://api.paystack.co/transaction', [
                'subaccount' => $subaccountCode,

                'perPage' => 100,
            ]);

            if (!$response->successful()) {
                Log::error('Paystack subaccount transactions fetch failed', [
                    'response' => $response->json(),
                    'subaccount' => $subaccountCode
                ]);
                return ['data' => [], 'error' => 'Failed to fetch transactions'];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error fetching Paystack subaccount transactions: ' . $e->getMessage());
            return ['data' => [], 'error' => $e->getMessage()];
        }
    }
}
