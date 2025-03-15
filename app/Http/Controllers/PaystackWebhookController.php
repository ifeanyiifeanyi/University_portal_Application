<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\StudentRecurringSubscription;
use App\Services\RecurringPaymentAdminService;

class PaystackWebhookController extends Controller
{
    public function __construct(private RecurringPaymentAdminService $recurringPaymentService)
    {

    }

    public function handleWebhook(Request $request)
    {
        // Verify Paystack webhook signature
        if (!$this->verifyWebhookSignature($request)) {
            return response()->json(['status' => 'invalid signature'], 400);
        }

        // Get the event and data
        $event = $request->input('event');
        $data = $request->input('data');

        // Log the webhook for debugging
        Log::channel('paystack')->info('Webhook received', [
            'event' => $event,
            'data' => $data
        ]);

        try {
            // Handle the event
            switch ($event) {
                case 'charge.success':
                    return $this->handleSuccessfulCharge($data);

                case 'transfer.success':
                    return $this->handleSuccessfulTransfer($data);

                case 'transfer.failed':
                    return $this->handleFailedTransfer($data);

                default:
                    Log::info('Unhandled Paystack webhook event: ' . $event);
                    return response()->json(['status' => 'unhandled event']);
            }
        } catch (\Exception $e) {
            Log::error('Error processing Paystack webhook', [
                'error' => $e->getMessage(),
                'event' => $event
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    protected function verifyWebhookSignature(Request $request)
    {
        $paystackSignature = $request->header('x-paystack-signature');
        $calculatedSignature = hash_hmac('sha512', $request->getContent(), env('PAYSTACK_SECRET_KEY'));

        return hash_equals($calculatedSignature, $paystackSignature);
    }

    protected function handleSuccessfulCharge(array $data)
    {
        // Extract subscription ID from metadata
        $metadata = $data['metadata'] ?? [];
        $subscriptionId = $metadata['subscription_id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Subscription ID not found in webhook data');
            return response()->json(['status' => 'error'], 400);
        }

        // Find the subscription
        $subscription = StudentRecurringSubscription::find($subscriptionId);

        if (!$subscription) {
            Log::error('Subscription not found', ['subscription_id' => $subscriptionId]);
            return response()->json(['status' => 'error'], 404);
        }

        // Process the payment
        $amount = ($data['amount'] / 100) - 500; // Remove processing fee and convert from kobo
        $this->recurringPaymentService->processPayment(
            $subscription,
            $amount,
            'online'
        );

        return response()->json(['status' => 'success']);
    }

    protected function handleSuccessfulTransfer(array $data)
    {
        Log::info('Transfer successful', $data);
        return response()->json(['status' => 'success']);
    }

    protected function handleFailedTransfer(array $data)
    {
        Log::error('Transfer failed', $data);
        return response()->json(['status' => 'success']);
    }
}
