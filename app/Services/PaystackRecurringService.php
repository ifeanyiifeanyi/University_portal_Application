<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\StudentRecurringSubscription;

class PaystackRecurringService
{

    protected $secretKey;
    protected $baseUrl = 'https://api.paystack.co';
    protected $processingFee = 500; // ₦500 processing fee
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->secretKey = env('PAYSTACK_SECRET_KEY');
    }


    public function initiatePayment(StudentRecurringSubscription $subscription, $paymentMethod)
    {
        // Get fresh data from database to ensure we have the latest values
        $subscription = $subscription->fresh();

        // Log the initial subscription details
        Log::info('Initiating payment for subscription #' . $subscription->id, [
            'subscription_id' => $subscription->id,
            'student_id' => $subscription->student_id,
            'total_amount' => $subscription->total_amount,
            'payment_method' => $paymentMethod
        ]);

        // Ensure we have a valid amount
        if ($subscription->total_amount <= 0) {
            Log::error('Invalid subscription amount detected', [
                'subscription_id' => $subscription->id,
                'total_amount' => $subscription->total_amount
            ]);
            return [
                'status' => false,
                'message' => 'Invalid subscription amount. Please contact support.'
            ];
        }

        // Calculate total amount including processing fee (amount in kobo)
        $totalAmount = $subscription->total_amount + $this->processingFee;
        $amountInKobo = (int)($totalAmount * 100);

        // Generate a unique reference
        $reference = 'SUB-' . $subscription->id . '-' . time();

        // Log the payment amount details
        Log::info('Payment details', [
            'reference' => $reference,
            'subscription_amount' => $subscription->total_amount,
            'processing_fee' => $this->processingFee,
            'total_amount' => $totalAmount,
            'amount_in_kobo' => $amountInKobo
        ]);

        // Get student details safely
        $studentName = 'Unknown';
        $studentEmail = 'student@example.com';
        $studentMatric = 'N/A';

        if ($subscription->student && $subscription->student->user) {
            $student = $subscription->student;
            $user = $student->user;
            $studentName = $user->first_name . ' ' . $user->last_name;
            $studentEmail = $user->email ?? 'student@example.com';
            $studentMatric = $student->matric_number ?? 'N/A';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'amount' => $amountInKobo,
                'email' => $studentEmail,
                'reference' => $reference,
                'subaccount' => "ACCT_ja7oconyxdp7dpy",
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'student_id' => $subscription->student_id,
                    'payment_type' => 'recurring_payment',
                    'subscription_amount' => $subscription->total_amount,
                    'processing_fee' => $this->processingFee,
                    'custom_fields' => [
                        [
                            'display_name' => "Student Name",
                            'variable_name' => "student_name",
                            'value' => $studentName
                        ],
                        [
                            'display_name' => "Student ID",
                            'variable_name' => "student_id",
                            'value' => $studentMatric
                        ],
                        [
                            'display_name' => "Payment Description",
                            'variable_name' => "payment_description",
                            'value' => "Payment for " . ($subscription->plan->name ?? 'Subscription Plan')
                        ]
                    ]
                ],
                'channels' => $this->getPaymentChannels($paymentMethod)
            ]);

            // Log the Paystack response
            Log::info('Paystack response', [
                'reference' => $reference,
                'response' => $response->json()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            // Log any exceptions that occur
            Log::error('Paystack payment initialization failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ];
        }
    }



    private function getPaymentChannels($paymentMethod)
    {
        return match ($paymentMethod) {
            'bank_transfer' => ['bank_transfer'],
            'card' => ['card'],
            default => ['card', 'bank_transfer', 'ussd', 'qr', 'bank', 'mobile_money']
        };
    }

    public function verifyTransaction($reference)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . "/transaction/verify/{$reference}");

        return $response->json();
    }
}
