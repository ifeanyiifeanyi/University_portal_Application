<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaystackRecurringService;
use Illuminate\Support\Facades\Log;

class PaymentVerificationController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackRecurringService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function verify(Request $request)
    {
        // Get the reference from the request
        $reference = $request->query('reference');
        
        if (!$reference) {
            return response()->json([
                'status' => false,
                'message' => 'No reference provided'
            ]);
        }
        
        try {
            // Verify the transaction with Paystack
            $verification = $this->paystackService->verifyTransaction($reference);
            
            Log::info('Payment verification attempt', [
                'reference' => $reference,
                'verification_result' => $verification
            ]);
            
            if ($verification['status'] && $verification['data']['status'] === 'success') {
                // Extract metadata from verification
                $metadata = $verification['data']['metadata'] ?? [];
                $subscriptionId = $metadata['subscription_id'] ?? null;
                
                return response()->json([
                    'status' => true,
                    'message' => 'Payment verified successfully',
                    'data' => [
                        'amount' => $verification['data']['amount'] / 100,
                        'subscription_id' => $subscriptionId,
                        'reference' => $reference
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment verification failed',
                    'data' => $verification
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error verifying payment', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage()
            ]);
        }
    }
}