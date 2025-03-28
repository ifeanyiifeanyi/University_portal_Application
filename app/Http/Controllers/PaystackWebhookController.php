<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentPaymentGatewayService;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    protected $paymentService;

    public function __construct(StudentPaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming Paystack webhook
     */
    public function handle(Request $request)
    {
        try {
            // Get raw payload and signature
            $payload = $request->getContent();
            $sigHeader = $request->header('x-paystack-signature');

            // Process webhook
            $result = $this->paymentService->handlePaystackWebhook($payload, $sigHeader);

            // Return appropriate response
            return response()->json([
                'status' => 'success',
                'message' => $result['message'] ?? 'Webhook processed'
            ], 200);
        } catch (\Exception $e) {
            // Log error and return error response
            Log::error('Paystack Webhook Error', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed'
            ], 400);
        }
    }
}
