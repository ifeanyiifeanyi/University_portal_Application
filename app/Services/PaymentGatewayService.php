<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\RemitaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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


    public function initializePayment(Payment $payment)
    {
        $gateway = $payment->paymentMethod->config['gateway'];

        switch ($gateway) {
            case 'paystack':
                return $this->initializePaystackPayment($payment);
            case 'remita':
                return $this->initializeRemitaPayment($payment);
            default:
                throw new \Exception("Unsupported payment gateway: {$gateway}");
        }
    }

    private function initializePaystackPayment(Payment $payment)
    {
        try {
            if (empty($this->paystackSecretKey)) {
                Log::error('Paystack secret key is empty');
                throw new \Exception('Paystack configuration is missing');
            }

            $paymentType = $payment->paymentType;

            // Calculate the minimum amount needed for Paystack fees
            // Paystack charges 1.5% + NGN 100 for transactions
            $paystackFeePercentage = 0.015; // 1.5%
            $paystackFixedFee = 100; // NGN 100

            $paymentData = [
                'amount' => $payment->amount * 100, // Convert to kobo
                'email' => $payment->student->user->email,
                'reference' => $payment->transaction_reference,
                'callback_url' => route('payment.verify', ['gateway' => 'paystack']),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'payment_type' => $payment->paymentType->name
                ]
            ];

            // Handle subaccount configuration
            if ($paymentType->paystack_subaccount_code) {
                // Calculate the maximum safe subaccount percentage
                $amount = $payment->amount;
                $paystackFee = ($amount * $paystackFeePercentage) + $paystackFixedFee;
                $maxSubaccountPercentage = (($amount - $paystackFee) / $amount) * 100;

                // Use either the configured percentage or the maximum safe percentage, whichever is lower
                $subaccountPercentage = min(
                    (float)$paymentType->subaccount_percentage,
                    floor($maxSubaccountPercentage)
                );

                Log::info('Setting up subaccount configuration', [
                    'subaccount_code' => $paymentType->paystack_subaccount_code,
                    'original_percentage' => $paymentType->subaccount_percentage,
                    'adjusted_percentage' => $subaccountPercentage,
                    'max_safe_percentage' => $maxSubaccountPercentage
                ]);

                // Set up split payment structure with adjusted percentage
                $paymentData['split'] = [
                    'type' => 'percentage',
                    'bearer_type' => 'account',
                    'subaccounts' => [
                        [
                            'subaccount' => $paymentType->paystack_subaccount_code,
                            'share' => $subaccountPercentage
                        ]
                    ]
                ];
            }

            $authHeader = 'Bearer ' . trim($this->paystackSecretKey);

            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', $paymentData);

            Log::debug('Paystack API Request Data:', array_merge(
                $paymentData,
                ['amount' => $paymentData['amount'] / 100] // Log amount in main currency
            ));

            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['status']) {
                    return $responseData['data']['authorization_url'];
                }
            }

            Log::error('Paystack payment initialization failed', [
                'payment_id' => $payment->id,
                'response' => $response->json()
            ]);

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

            Log::debug('Paystack verification response', [
                'reference' => $reference,
                'status_code' => $response->status(),
                'response_body' => $response->json()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Check if the response has the expected structure
                if (!isset($responseData['data']['status'])) {
                    throw new \Exception('Invalid response structure from Paystack');
                }

                // Check all possible successful status values from Paystack
                $successStatuses = ['success', 'completed'];
                if ($responseData['status'] && in_array(strtolower($responseData['data']['status']), $successStatuses)) {
                    // Find the payment record
                    $payment = Payment::where('transaction_reference', $reference)->first();

                    if (!$payment) {
                        throw new \Exception('Payment record not found');
                    }

                    // Convert amount back from kobo
                    $paidAmount = $responseData['data']['amount'] / 100;

                    // Allow for a small difference in amount (e.g., due to currency conversion)
                    $expectedAmount = $payment->amount;
                    $difference = abs($paidAmount - $expectedAmount);
                    $allowedDifference = 1; // Allow 1 unit difference

                    if ($difference > $allowedDifference) {
                        Log::warning('Payment amount mismatch', [
                            'reference' => $reference,
                            'expected' => $expectedAmount,
                            'received' => $paidAmount
                        ]);
                        throw new \Exception('Payment amount mismatch');
                    }

                    // Store additional transaction details
                    $payment->update([
                        'payment_reference' => $responseData['data']['reference'],
                        'gateway_response' => json_encode($responseData['data']),
                        'payment_channel' => $responseData['data']['channel'] ?? 'unknown'
                    ]);

                    Log::info('Payment verification successful', [
                        'reference' => $reference,
                        'amount' => $paidAmount
                    ]);

                    return [
                        'success' => true,
                        'reference' => $reference,
                        'amount' => $paidAmount,
                        'metadata' => [
                            'channel' => $responseData['data']['channel'] ?? 'unknown',
                            'card_type' => $responseData['data']['authorization']['card_type'] ?? null,
                            'bank' => $responseData['data']['authorization']['bank'] ?? null,
                        ]
                    ];
                }

                Log::warning('Payment verification failed - Invalid status', [
                    'reference' => $reference,
                    'status' => $responseData['data']['status'] ?? 'unknown'
                ]);
            }

            // If we get here, verification was not successful
            Log::error('Payment verification failed - API error', [
                'reference' => $reference,
                'response' => $response->json()
            ]);

            return ['success' => false];
        } catch (\Exception $e) {
            Log::error('Payment verification error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false];
        }
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
