<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class RemitaService
{
    protected $baseUrl;
    protected $merchantId;
    protected $apiKey;
    protected $serviceTypeId;
    protected $publicKey;
    protected $secretKey;
    protected $environment;

    public function __construct()
    {
        $this->environment = Config::get('services.remita.environment', 'demo');
        $this->merchantId = Config::get('services.remita.merchant_id');
        $this->apiKey = Config::get('services.remita.api_key');
        $this->serviceTypeId = Config::get('services.remita.service_type_id');
        $this->publicKey = Config::get('services.remita.public_key');
        $this->secretKey = Config::get('services.remita.secret_key');
        $this->baseUrl = $this->getBaseUrl();
    }

    protected function getBaseUrl()
    {
        return $this->environment === 'live'
            ? 'https://login.remita.net/remita/exapp/api/v1/send/api'
            : 'https://demo.remita.net/remita/exapp/api/v1/send/api
';
    }

    public function initializeStudentPayment($paymentDetails)
    {
        $orderId = $this->generateOrderId();
        $hash = $this->generateHash($orderId, $paymentDetails['amount']);

        $payload = [
            'merchantId' => $this->merchantId,
            'serviceTypeId' => $this->serviceTypeId,
            'orderId' => $orderId,
            'payerName' => $paymentDetails['studentName'],
            'payerEmail' => $paymentDetails['studentEmail'],
            'payerPhone' => $paymentDetails['studentPhone'],
            'description' => $paymentDetails['description'],
            'amount' => $paymentDetails['amount'],
            'hash' => $hash,
            'customFields' => [
                ['name' => 'studentId', 'value' => $paymentDetails['studentId']],
                ['name' => 'feeType', 'value' => $paymentDetails['feeType']],
                ['name' => 'semester', 'value' => $paymentDetails['semester']],
                ['name' => 'academicYear', 'value' => $paymentDetails['academicYear']],
            ],
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'remitaConsumerKey=' . $this->merchantId . ',remitaConsumerToken=' . $this->generateToken($payload)
        ];

        try {
            Log::info('Initiating Remita payment', ['payload' => $payload, 'url' => $this->baseUrl]);
            $response = Http::withHeaders($headers)->post($this->baseUrl, $payload);

            if ($response->successful()) {
                Log::info('Remita payment initialization successful', ['response' => $response->json()]);
                return [
                    'status' => 'success',
                    'data' => $response->json(),
                ];
            } else {
                Log::error('Remita API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $this->baseUrl,
                    'payload' => $payload
                ]);
                return [
                    'status' => 'error',
                    'message' => 'Failed to initialize payment with Remita. Status: ' . $response->status(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Remita API exception', [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl,
                'payload' => $payload
            ]);
            return [
                'status' => 'error',
                'message' => 'An error occurred while communicating with Remita: ' . $e->getMessage(),
            ];
        }
    }

    public function verifyPayment($orderId)
    {
        $hash = hash('sha512', $orderId . $this->apiKey . $this->merchantId);
        $verifyUrl = $this->getVerifyUrl($orderId, $hash);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'remitaConsumerKey=' . $this->merchantId . ',remitaConsumerToken=' . $this->generateToken(['orderId' => $orderId])
        ];

        try {
            Log::info('Verifying Remita payment', ['orderId' => $orderId, 'url' => $verifyUrl]);
            $response = Http::withHeaders($headers)->get($verifyUrl);

            if ($response->successful()) {
                Log::info('Remita payment verification successful', ['response' => $response->json()]);
                return [
                    'status' => 'success',
                    'data' => $response->json(),
                ];
            } else {
                Log::error('Remita verification API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $verifyUrl
                ]);
                return [
                    'status' => 'error',
                    'message' => 'Failed to verify payment with Remita. Status: ' . $response->status(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Remita verification API exception', [
                'message' => $e->getMessage(),
                'url' => $verifyUrl
            ]);
            return [
                'status' => 'error',
                'message' => 'An error occurred while verifying payment with Remita: ' . $e->getMessage(),
            ];
        }
    }

    protected function generateOrderId()
    {
        return 'STUFEE' . time() . rand(10, 99);
    }

    protected function generateHash($orderId, $amount)
    {
        return hash('sha512', $this->merchantId . $this->serviceTypeId . $orderId . $amount . $this->apiKey);
    }

    protected function generateToken($payload)
    {
        $hash = hash('sha512', $this->apiKey . $this->serviceTypeId . json_encode($payload));
        return hash('sha512', $hash . $this->secretKey);
    }

    protected function getVerifyUrl($orderId, $hash)
    {
        $baseVerifyUrl = $this->environment === 'live'
            ? 'https://login.remita.net/remita/ecomm'
            : 'https://demo.remita.net/remita/ecomm';

        return "{$baseVerifyUrl}/{$this->merchantId}/{$orderId}/{$hash}/orderstatus.reg";
    }
}
