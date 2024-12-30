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

    private function initializePaystackPayment(Payment $payment, $amount = null)
    {
        try {
            if (empty($this->paystackSecretKey)) {
                throw new \Exception('Paystack configuration is missing');
            }

            $paymentType = $payment->paymentType;

            // Use the explicitly passed amount or fall back to the payment's current transaction amount
            $amountToCharge = ($amount ?? $payment->next_transaction_amount ?? $payment->amount) * 100; // Convert to kobo

            // Calculate the minimum amount needed for Paystack fees
            // Paystack charges 1.5% + NGN 100 for transactions
            $paystackFeePercentage = 0.015; // 1.5%
            $paystackFixedFee = 100; // NGN 100

            $paymentData = [
                'amount' => $amountToCharge,
                'first_name' => $payment->student->user->first_name,
                'last_name' => $payment->student->user->last_name,
                'email' => $payment->student->user->email,
                'phone' => $payment->student->user->phone,
                'reference' => $payment->transaction_reference,
                'callback_url' => route('payment.verify', ['gateway' => 'paystack']),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'payment_type' => $payment->paymentType->name,
                    'is_installment' => $payment->is_installment,
                    'installment_number' => $payment->is_installment ? 1 : null,
                ]
            ];

            // Handle subaccount configuration
            if ($paymentType->paystack_subaccount_code) {
                // Calculate the maximum safe subaccount percentage
                $amount = $amountToCharge / 100; // Convert back to main currency for calculation
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

            // Convert amount from kobo
            $paidAmount = $responseData['data']['amount'] / 100;

            // Determine expected amount based on installment status
            $expectedAmount = $payment->is_installment
                ? $payment->next_transaction_amount
                : $payment->amount;

            // Allow for a small difference in amount
            $difference = abs($paidAmount - $expectedAmount);
            $allowedDifference = 1; // Allow 1 unit difference

            if ($difference > $allowedDifference) {
                throw new \Exception(sprintf(
                    'Payment amount mismatch. Expected: %s, Received: %s',
                    $expectedAmount,
                    $paidAmount
                ));
            }

            // Handle installment payment updates
            if ($payment->is_installment) {
                $this->handleInstallmentPayment($payment, $paidAmount, $responseData);
            } else {
                $this->handleFullPayment($payment, $paidAmount, $responseData);
            }

            return [
                'success' => true,
                'reference' => $reference,
                'amount' => $paidAmount,
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
