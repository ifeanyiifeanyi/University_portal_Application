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
        $this->paystackSecretKey = config('services.paystack.secret_key');
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


    private function initializePaystackPayments(Payment $payment)
    {




        $data = [
            "amount" => $payment->amount * 100, // Amount in kobo
            "email" => $payment->student->user->email,
            "reference" => $payment->transaction_reference,
            "callback_url" => route('payment.verify', ['gateway' => 'paystack']),
        ];

        try {
            $authorization = Paystack::getAuthorizationUrl($data);
            return $authorization->url; // Return only the URL, not the entire response
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization failed: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paystack payment');
        }
    }
    private function initializePaystackPayment(Payment $payment)
    {
        try {
            $paymentType = $payment->paymentType;

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

            // Add subaccount configuration if available
            if ($paymentType->paystack_subaccount_code) {
                $paymentData['subaccount'] = $paymentType->paystack_subaccount_code;
                $paymentData['bearer'] = 'subaccount'; // or 'subaccount' depending on who bears the transaction fee

                // If there's a specific split percentage for this payment type
                if ($paymentType->subaccount_percentage) {
                    $paymentData['split'] = [
                        'type' => 'percentage',
                        'subaccount' => $paymentType->paystack_subaccount_code,
                        'share' => $paymentType->subaccount_percentage
                    ];
                }
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', $paymentData);

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

            throw new \Exception('Failed to initialize payment');
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paystack payment');
        }
    }

    // private function initializeRemitaPayment(Payment $payment)
    // {
    //     $paymentDetails = [
    //         'studentName' => $payment->student->user->full_name,
    //         'studentEmail' => $payment->student->user->email,
    //         'studentPhone' => $payment->student->user->phone,
    //         'description' => $payment->paymentType->name,
    //         'amount' => $payment->amount,
    //         'studentId' => $payment->student->id,
    //         'feeType' => $payment->paymentType->name,
    //         'semester' => $payment->semester->name,
    //         'academicYear' => $payment->academicSession->name,
    //     ];

    //     $response = $this->remitaService->initializeStudentPayment($paymentDetails);

    //     if ($response['status'] === 'success') {
    //         return redirect()->away($response['data']['paymentUrl']);
    //     } else {
    //         Log::error('Remita payment initialization failed: ' . $response['message']);
    //         throw new \Exception('Failed to initialize Remita payment');
    //     }
    // }

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

    private function verifyPaystackPayments($reference)
    {
        $paymentDetails = Paystack::getPaymentData();

        if ($paymentDetails['status'] && $paymentDetails['data']['status'] === 'success') {
            return [
                'success' => true,
                'reference' => $reference,
                'amount' => $paymentDetails['data']['amount'] / 100,
            ];
        }

        return ['success' => false];
    }

    private function verifyPaystackPayment($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['status'] && $responseData['data']['status'] === 'success') {
                    // Verify the amount matches
                    $payment = Payment::where('transaction_reference', $reference)->first();
                    $expectedAmount = $payment->amount * 100; // Convert to kobo

                    if ($responseData['data']['amount'] === $expectedAmount) {
                        // Store additional transaction details
                        $payment->update([
                            'payment_reference' => $responseData['data']['reference'],
                            'gateway_response' => json_encode($responseData['data']),
                            'payment_channel' => $responseData['data']['channel']
                        ]);

                        return [
                            'success' => true,
                            'reference' => $reference,
                            'amount' => $responseData['data']['amount'] / 100,
                        ];
                    }
                }
            }

            return ['success' => false];
        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
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
}
