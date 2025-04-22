<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecurringPaymentPlan;
use App\Models\StudentRecurringSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;
use App\Models\Student;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use App\Services\PaystackRecurringService;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class StudentRecurringPaymentsController extends Controller
{

    protected $authService;
    protected $paystackService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService, PaystackRecurringService $paystackService)
    {

        $this->authService = $authService;
        $this->paystackService = $paystackService;
    }
    public function index(){
        $student = Student::where('user_id', $this->authService->user()->id)->first();
        $subscriptions = StudentRecurringSubscription::with('student')->where('student_id',$student->id)->get();
        // Only show active payment plans
        $paymentPlans = RecurringPaymentPlan::where('is_active', true)->get();
        return view('student.recurring.index', compact('subscriptions', 'paymentPlans'));
    }

 

    public function showPaymentPage(RecurringPaymentPlan $plan)
    {
        $student = Student::where('user_id', $this->authService->user()->id)->first();
        $paymentMethods = PaymentMethod::where('is_active', 1)->get();
    
        // Get all paid months across all subscriptions for this student and plan
        $paidMonths = [];
        $subscriptions = StudentRecurringSubscription::where('student_id', $student->id)
            ->where('recurring_payment_plan_id', $plan->id)
            ->get();
    
        foreach ($subscriptions as $subscription) {
            if (!empty($subscription->selected_months)) {
                $paidMonths = array_merge($paidMonths, $subscription->selected_months);
            }
        }
    
        // Remove duplicates and ensure values are strings (to match checkbox values)
        $paidMonths = array_unique($paidMonths);
        $paidMonths = array_map('strval', $paidMonths);
    
        return view('student.recurring.makepayment', compact('plan', 'student', 'paymentMethods', 'paidMonths'));
    }

//     public function processPayment(Request $request)
// {
//     $request->validate([
//         'recurring_plan_id' => 'required|exists:recurring_payment_plans,id',
//         'amount_per_month' => 'required|numeric|min:1',
//         'number_of_payments' => 'required|integer|min:1|max:12',
//         'total_amount' => 'required|numeric|min:1',
//         'starting_month' => 'required|numeric|min:1|max:12',
//         'ending_month' => 'required|numeric|min:1|max:12',
//     ]);

//     $startingMonth = (int) $request->starting_month;
//     $endingMonth = (int) $request->ending_month;
    
//     // Get current year
//     $currentYear = Carbon::now()->year;
    
//     // Create Carbon instances for start and end dates
//     $startDate = Carbon::createFromDate($currentYear, $startingMonth, 1)->startOfMonth();
//     $endDate = Carbon::createFromDate($currentYear, $endingMonth, 1)->endOfMonth();
    
//     // Handle year wrapping if ending month is before starting month
//     if ($endingMonth < $startingMonth) {
//         $endDate->addYear();
//     }
    
//     // Calculate months between (including both start and end months)
//     // Using a different approach to avoid floating point issues
//     $startMonth = $startDate->month + ($startDate->year * 12);
//     $endMonth = $endDate->month + ($endDate->year * 12);
//     $monthsBetween = $endMonth - $startMonth + 1; // +1 to include both start and end months
    
//     // Get month names for all months in the range
//     $monthsInRange = [];
//     $tempDate = clone $startDate;
    
//     for ($i = 0; $i < $monthsBetween; $i++) {
//         $monthsInRange[] = $tempDate->format('F');
//         $tempDate->addMonth();
//     }

//     try {
//         DB::beginTransaction();
        
//         // Get payment method
//         // $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
//         $paymentChannel = 'online_card';
//         // $currentDate = Carbon::now();
// // $endDate = $currentDate->addMonth((int)$request->number_of_payments);

//         // Create subscription record
//         $subscription = StudentRecurringSubscription::create([
//             'student_id' => auth()->user()->student->id,
//             'recurring_payment_plan_id' => $request->recurring_plan_id,
//             'amount_per_month' => $request->amount_per_month,

//             'total_amount' => $request->total_amount,
//             'amount_paid' => 0, // Initially 0 since payment hasn't been confirmed
//             'number_of_payments' => $monthsBetween,
//             'payment_method'=>'online_card',
//             'balance' => $request->total_amount, // Initialize balance to full amount
//             'start_date' => $startDate->toDateString(),
//             'end_date'=>$endDate->toDateString(),
//             'is_active' => false, // Will be activated after payment
//             'payment_history' => json_encode([]) // Initialize empty payment history
//         ]);

//         // Initiate payment with Paystack
//         $response = $this->paystackService->initiatePayment($subscription, $paymentChannel);

//         if ($response['status']) {
//             // Store the initialization response in payment_history
//             $paymentHistory = json_decode($subscription->payment_history, true) ?: [];
//             $paymentHistory[] = [
//                 'type' => 'initialization',
//                 'date' => now()->toDateTimeString(),
//                 'reference' => $response['data']['reference'],
//                 'response' => $response
//             ];
            
//             $subscription->payment_history = json_encode($paymentHistory);
//             $subscription->save();
            
//             DB::commit();
            
//             // Redirect to Paystack payment page
//             return redirect($response['data']['authorization_url']);
//         } else {
//             throw new Exception($response['message'] ?? 'Payment initialization failed');
//         }
//     } catch (Exception $e) {
//         DB::rollBack();
        
//         Log::error('Recurring payment processing failed', [
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);
        
//         return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
//     }
// }

public function processPayment(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'recurring_plan_id' => 'required|exists:recurring_payment_plans,id',
        'amount_per_month' => 'required|numeric|min:1',
        'total_amount' => 'required|numeric|min:1',
        'selected_months' => 'required|string',
    ]);

    // Decode the selected months
    $selectedMonths = json_decode($request->selected_months, true);
    
    // Validate that selected months is a non-empty array
    if (!is_array($selectedMonths) || empty($selectedMonths)) {
        return redirect()->back()->with('error', 'Please select at least one month for payment.');
    }

    // Get current year
    $currentYear = Carbon::now()->year;
    
    // Create Carbon instances for start and end dates
    $selectedMonthObjects = collect($selectedMonths)
        ->map(function($monthNumber) use ($currentYear) {
            return Carbon::createFromDate($currentYear, $monthNumber, 1);
        })
        ->sort();

    // Determine start and end dates
    $startDate = $selectedMonthObjects->first()->startOfMonth();
    $endDate = $selectedMonthObjects->last()->endOfMonth();

    // Retrieve the recurring payment plan
    $recurringPlan = RecurringPaymentPlan::findOrFail($request->recurring_plan_id);

    try {
        DB::beginTransaction();
        
        // Default payment channel
        $paymentChannel = 'online_card';

        // Get the authenticated student
        $student = auth()->user()->student;

        // Create subscription record
        $subscription = StudentRecurringSubscription::create([
            'student_id' => $student->id,
            'recurring_payment_plan_id' => $recurringPlan->id,
            'amount_per_month' => $recurringPlan->amount,
            'total_amount' => $request->total_amount,
            'amount_paid' => 0,
            'number_of_payments' => count($selectedMonths),
            'payment_method' => $paymentChannel,
            'balance' => $request->total_amount,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'is_active' => false,
            'payment_history' => json_encode([]), // Keep existing payment_history empty
            'selected_months' => $selectedMonths // Store selected months
        ]);

      
        $response = $this->paystackService->initiatePayment(
            $subscription, 
            $paymentChannel
        );

        // Handle payment initialization
        if ($response['status']) {
            // Log initialization details
            $initializationLog = [
                'type' => 'payment_initialization',
                'date' => now()->toDateTimeString(),
                'reference' => $response['data']['reference'],
                'amount' => $request->total_amount,
                'selected_months' => $selectedMonths
            ];

            // You might want to log this separately or in a different way depending on your requirements
            Log::info('Recurring Payment Initialized', $initializationLog);
            
            DB::commit();
            
            // Redirect to Paystack payment page
            return redirect($response['data']['authorization_url']);
        } else {
            // Throw an exception if payment initialization fails
            throw new \Exception($response['message'] ?? 'Payment initialization failed');
        }
    } catch (\Exception $e) {
        // Rollback the transaction
        DB::rollBack();
        
        // Log the error
        Log::error('Recurring payment processing failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        // Redirect back with error message
        return redirect()->back()
            ->with('error', 'Payment processing failed: ' . $e->getMessage());
    }
}


public function continuePayment($id)
{
    try {
        // Find the subscription
        $subscription = StudentRecurringSubscription::findOrFail($id);
        
        // Check if this subscription belongs to the authenticated user
        if ($subscription->student_id != auth()->user()->student->id) {
            return redirect()->back()->with('error', 'You do not have permission to access this subscription.');
        }
        
        // Check if subscription is already active
        if ($subscription->is_active) {
            return redirect()->route('student.recurring.receipt', ['id' => $subscription->id])
                ->with('info', 'Your subscription is already active.');
        }
        
        // Initiate payment with Paystack again
        $paymentChannel = 'online_card';
        $response = $this->paystackService->initiatePayment($subscription, $paymentChannel);
        
        if ($response['status']) {
            // Update the payment history with this new attempt
            $paymentHistory = json_decode($subscription->payment_history, true) ?: [];
            $paymentHistory[] = [
                'type' => 'continuation',
                'date' => now()->toDateTimeString(),
                'reference' => $response['data']['reference'],
                'response' => $response
            ];
            
            $subscription->payment_history = json_encode($paymentHistory);
            $subscription->save();
            
            // Redirect to Paystack payment page
            return redirect($response['data']['authorization_url']);
        } else {
            throw new Exception($response['message'] ?? 'Payment continuation failed');
        }
    } catch (Exception $e) {
        Log::error('Recurring payment continuation failed', [
            'subscription_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()->with('error', 'Payment continuation failed: ' . $e->getMessage());
    }
}
public function handleCallback(Request $request)
{
    $reference = $request->query('reference');
    
    if (!$reference) {
        return redirect()->route('student.recurring.plans')
            ->with('error', 'No reference provided for payment verification');
    }
    
    DB::beginTransaction();
    
    try {
        // First try to find the reference in payment_history using a simpler approach
        $found = false;
        $subscription = null;
        
        // Get all subscriptions and check their payment history manually
        $subscriptions = StudentRecurringSubscription::with(['student.user', 'plan'])->get();
        
        // foreach ($subscriptions as $sub) {
        //     $history = json_decode($sub->payment_history, true) ?: [];
            
        //     foreach ($history as $entry) {
        //         if (isset($entry['reference']) && $entry['reference'] === $reference) {
        //             $subscription = $sub;
        //             $found = true;
        //             break 2;
        //         }
        //     }
        // }
        foreach ($subscriptions as $sub) {
            // Check if payment_history is already an array
            $history = is_array($sub->payment_history) 
                ? $sub->payment_history 
                : (json_decode($sub->payment_history, true) ?: []);
            
            foreach ($history as $entry) {
                if (isset($entry['reference']) && $entry['reference'] === $reference) {
                    $subscription = $sub;
                    $found = true;
                    break 2;
                }
            }
        }
        
        if (!$found || !$subscription) {
            // Try to get subscription from metadata in Paystack verification
            $paystackResult = $this->paystackService->verifyTransaction($reference);
            
            if (isset($paystackResult['status']) && $paystackResult['status'] && 
                isset($paystackResult['data']['metadata']['subscription_id'])) {
                
                $subscriptionId = $paystackResult['data']['metadata']['subscription_id'];
                $subscription = StudentRecurringSubscription::with(['student.user', 'plan'])
                    ->find($subscriptionId);
                
                if (!$subscription) {
                    throw new Exception('No subscription found with ID from Paystack metadata');
                }
            } else {
                throw new Exception('No subscription found with the provided reference');
            }
        }
        
        // Verify the payment with Paystack
        $result = $this->paystackService->verifyTransaction($reference);
        
        if (!isset($result['status']) || !$result['status']) {
            throw new Exception('Payment verification failed: ' . ($result['message'] ?? 'Unknown error'));
        }
        
        // Add verification result to payment history
        $paymentHistory = json_decode($subscription->payment_history, true) ?: [];
        $paymentHistory[] = [
            'type' => 'verification',
            'date' => now()->toDateTimeString(),
            'reference' => $reference,
            'response' => $result
        ];
        
        // Check if payment is successful
        if ($result['data']['status'] === 'success') {
            // Calculate payment amount (excluding processing fee)
            $amountPaid = $result['data']['amount'] / 100; // Convert from kobo to naira
            $processingFee = 500; // Same as in PaystackRecurringService
            $actualPayment = $amountPaid - $processingFee;
            
            // Update subscription
            $subscription->amount_paid = $actualPayment;
            $subscription->balance = $subscription->total_amount - $actualPayment;
            $subscription->is_active = true;
            $subscription->payment_history = json_encode($paymentHistory);
            $subscription->save();
            
            DB::commit();
            
            // Redirect to receipt page with subscription ID
            return redirect()->route('student.recurring.receipt', ['id' => $subscription->id])
                ->with('success', 'Payment completed successfully');
        } else {
            // Payment was not successful
            $subscription->payment_history = json_encode($paymentHistory);
            $subscription->save();
            
            throw new Exception('Payment was not successful: ' . ($result['data']['gateway_response'] ?? 'Unknown error'));
        }
    } catch (Exception $e) {
        DB::rollBack();
        
        Log::error('Payment verification failed', [
            'reference' => $reference,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('student.recurring.plans')
            ->with('error', 'Payment verification failed: ' . $e->getMessage());
    }
}

/**
 * Show the receipt page
 */
// public function receipt($id)
// {
//     try {
//         $subscription = StudentRecurringSubscription::with(['student.user', 'plan'])
//             ->findOrFail($id);
        
//         // Get payment details from the payment history
//         $paymentHistory = json_decode($subscription->payment_history, true) ?: [];
//         $verificationData = null;
        
//         // Find the verification entry
//         foreach ($paymentHistory as $entry) {
//             if (isset($entry['type']) && $entry['type'] === 'verification') {
//                 $verificationData = $entry;
//                 break;
//             }
//         }

//         $start = Carbon::parse($subscription->start_date);
//     $end = Carbon::parse($subscription->end_date);

//     $months = [];
//     while ($start->lte($end)) {
//         $months[] = $start->format('F Y'); // Example: "January 2024"
//         $start->addMonth();
//     }
        
//         return view('student.recurring.receipt', [
//             'subscription' => $subscription,
//             'paymentData' => $verificationData,
//             'months'=>$months
//         ]);
        
//     } catch (Exception $e) {
//         return redirect()->back()
//             ->with('error', 'Could not find receipt information: ' . $e->getMessage());
//     }
// }

public function receipt($id) {
    try {
        $subscription = StudentRecurringSubscription::with(['student.user', 'plan'])
            ->findOrFail($id);

        // Get payment details from the payment history
        $paymentHistory = json_decode($subscription->payment_history, true) ?: [];
        $verificationData = null;

        // Find the verification entry
        foreach ($paymentHistory as $entry) {
            if (isset($entry['type']) && $entry['type'] === 'verification') {
                $verificationData = $entry;
                break;
            }
        }

        // Convert selected months to full month names with year
        $selectedMonths = $subscription->selected_months ?? [];
        $formattedMonths = collect($selectedMonths)
            ->map(function($monthNumber) {
                return date('F Y', mktime(0, 0, 0, $monthNumber, 1, date('Y')));
            })
            ->toArray();

                    $start = Carbon::parse($subscription->start_date);
    $end = Carbon::parse($subscription->end_date);

    $monthsinterval = [];
    while ($start->lte($end)) {
        $monthsinterval[] = $start->format('F Y'); // Example: "January 2024"
        $start->addMonth();
    }

        return view('student.recurring.receipt', [
            'subscription' => $subscription,
            'paymentData' => $verificationData,
            'months' => $formattedMonths,
            'monthsinterval'=>$monthsinterval
        ]);
    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Could not find receipt information: ' . $e->getMessage());
    }
}

/**
 * Get the payment channel for Paystack based on the payment method name
 */


private function getPaymentChannel($methodName)
{
    return match (strtolower($methodName)) {
        'bank transfer' => 'bank_transfer',
        'card', 'credit card', 'debit card' => 'card',
        default => 'default'
    };
}


}
