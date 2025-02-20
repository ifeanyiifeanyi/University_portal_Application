<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Student;
use App\Models\RecurringPaymentPlan;
use App\Models\StudentRecurringSubscription;

class RecurringPaymentAdminService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getStudentsByDepartmentAndLevel($departmentId, $level)
    {
        return Student::where('department_id', $departmentId)
            ->where('current_level', $level)
            ->with([
                'user', // Include the user relationship to access name fields
                'recurringSubscriptions' => function ($query) {
                    $query->where('is_active', true)
                        ->with('plan'); // Using the plan relationship from the model
                }
            ])
            ->get()
            ->map(function ($student) {
                // Add full name to student object
                $student->name = $student->user ?
                    $student->user->first_name . ' ' . $student->user->last_name . ' '. $student->user->other_name ?? '' :
                    'Unknown';

                // Add subscription status information
                $student->recurringSubscriptions->transform(function ($subscription) {
                    return [
                        'id' => $subscription->id,
                        'plan_name' => $subscription->plan->name,
                        'amount_per_month' => $subscription->amount_per_month,
                        'total_amount' => $subscription->total_amount,
                        'balance' => $subscription->balance,
                        'status' => $subscription->status, // Using the status accessor
                        'percentage_paid' => $subscription->percentage_paid, // Using the percentage accessor
                    ];
                });
                return $student;
            });
    }




    /**
     * Calculate payment details for a recurring payment plan
     */
    public function calculateRecurringPayment($numberOfPayments, $recurringPlanId)
    {
        $plan = RecurringPaymentPlan::findOrFail($recurringPlanId);
        $totalAmount = $plan->amount * $numberOfPayments;

        return [
            'amount_per_month' => $plan->amount,
            'total_amount' => $totalAmount,
            'number_of_payments' => $numberOfPayments,
            'start_date' => Carbon::now()->format('Y-m-d')
        ];
    }

      /**
     * Create or update student subscription
     */
    public function createSubscription(Student $student, $planId, $numberOfPayments, $paymentMethod = null)
    {
        $plan = RecurringPaymentPlan::findOrFail($planId);
        $totalAmount = $plan->amount_per_month * $numberOfPayments;

        // Check for existing active subscription
        $existingSubscription = $student->recurringSubscriptions()
            ->where('recurring_payment_plan_id', $planId)
            ->where('is_active', true)
            ->first();

        if ($existingSubscription) {
            // Add new payment to existing subscription
            $newTotal = $existingSubscription->total_amount + $totalAmount;
            $existingSubscription->update([
                'total_amount' => $newTotal,
                'balance' => $existingSubscription->balance + $totalAmount
            ]);

            // Record the additional payment
            $existingSubscription->recordPayment(
                $totalAmount,
                'ADD-' . uniqid()
            );

            return $existingSubscription;
        }

        // Create new subscription
        $subscription = new StudentRecurringSubscription([
            'student_id' => $student->id,
            'recurring_payment_plan_id' => $planId,
            'amount_per_month' => $plan->amount_per_month,
            'total_amount' => $totalAmount,
            'start_date' => Carbon::now(),
            'is_active' => true
        ]);

        $subscription->save(); // This will trigger the boot method to set initial values

        return $subscription;
    }


      /**
     * Process payment for a subscription
     */
    public function processPayment($subscription, $amount, $paymentMethod)
    {
        // Generate a unique reference
        $reference = strtoupper($paymentMethod) . '-' . uniqid();

        // Record the payment using the model's method
        $subscription->recordPayment($amount, $reference);

        // Check if subscription is fully paid
        if ($subscription->isPaid()) {
            // You might want to handle completion actions here
            // For example, sending notifications, generating receipts, etc.
        }

        return [
            'status' => $subscription->status,
            'reference' => $reference,
            'amount_paid' => $amount,
            'remaining_balance' => $subscription->balance,
            'percentage_paid' => $subscription->percentage_paid
        ];
    }

}
