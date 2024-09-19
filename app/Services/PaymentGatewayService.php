<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\RemitaService;
use Illuminate\Support\Facades\Log;
use Unicodeveloper\Paystack\Facades\Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class PaymentGatewayService
{
    protected $remitaService;

    public function __construct(RemitaService $remitaService)
    {
        $this->remitaService = $remitaService;
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

    private function verifyPaystackPayment($reference)
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
