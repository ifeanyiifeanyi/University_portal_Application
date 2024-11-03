<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\RemitaService;
use Illuminate\Support\Facades\Log;
use Unicodeveloper\Paystack\Facades\Paystack;
// use KingFlamez\Rave\Facades\Rave as Flutterwave;

class StudentPaymentGatewayService
{
    // protected $remitaService;

    // public function __construct(RemitaService $remitaService)
    // {
    //     $this->remitaService = $remitaService;
    // }


    public function initializePayment(Payment $payment)
    {
        $gateway = $payment->paymentMethod->config['gateway'];

        switch ($gateway) {
            case 'paystack':
                return $this->initializePaystackPayment($payment);
            case 'remita':
                return 'remitta';
                // return $this->initializeRemitaPayment($payment);
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
            "callback_url" => route('student.fees.payment.verify', ['gateway' => 'paystack']),
        ];

        try {
            $authorization = Paystack::getAuthorizationUrl($data);
            return $authorization->url; // Return only the URL, not the entire response
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization failed: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paystack payment');
        }
    }

    public function verifyPayment($gateway, $reference)
    {
        switch ($gateway) {
            case 'paystack':
                return $this->verifyPaystackPayment($reference);
            case 'remita':
                return 'verify remita';
                // return $this->verifyRemitaPayment($reference);
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
}