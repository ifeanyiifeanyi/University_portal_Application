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

        // Calculate coverage period based on start date and number of payments
        $startDate = Carbon::now();
        $endDate = (clone $startDate)->addMonths($numberOfPayments - 1)->endOfMonth();

        return [
            'amount_per_month' => $plan->amount,
            'total_amount' => $totalAmount,
            'number_of_payments' => $numberOfPayments,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ];
    }

      /**
     * Create or update student subscription
     */
    public function createSubscription(Student $student, $planId, $numberOfPayments, $paymentMethod = null)
    {
        $plan = RecurringPaymentPlan::findOrFail($planId);

        // FIX 1: Use the correct amount per month from the plan
        $amountPerMonth = $plan->amount;
        $totalAmount = $amountPerMonth * $numberOfPayments;

        // Calculate coverage dates
        $startDate = Carbon::now();
        $endDate = (clone $startDate)->addMonths($numberOfPayments - 1)->endOfMonth();

         // Create payment period details
         $paymentPeriods = [];
         $currentDate = clone $startDate;

         for ($i = 0; $i < $numberOfPayments; $i++) {
             $periodStart = clone $currentDate;
             $periodEnd = (clone $periodStart)->endOfMonth();

             $paymentPeriods[] = [
                 'month_number' => $i + 1,
                 'period' => $periodStart->format('F Y'),
                 'start_date' => $periodStart->format('Y-m-d'),
                 'end_date' => $periodEnd->format('Y-m-d'),
                 'amount' => $amountPerMonth,
                 'paid' => false,
                 'payment_date' => null,
                 'payment_reference' => null
             ];

             $currentDate->addMonth();
         }

        // Check for existing active subscription
        $existingSubscription = $student->recurringSubscriptions()
            ->where('recurring_payment_plan_id', $planId)
            ->where('is_active', true)
            ->first();

            if ($existingSubscription) {
                // Add new payment to existing subscription
                $newTotal = $existingSubscription->total_amount + $totalAmount;

                // Get existing payment history
                $paymentHistory = $existingSubscription->payment_history ?? [];

                // Determine the last month in existing payment history
                $lastMonthNumber = 0;
                if (!empty($paymentHistory)) {
                    foreach ($paymentHistory as $entry) {
                        if (isset($entry['month_number']) && $entry['month_number'] > $lastMonthNumber) {
                            $lastMonthNumber = $entry['month_number'];
                        }
                    }
                }

                // Adjust month numbers for new payment periods
                foreach ($paymentPeriods as &$period) {
                    $period['month_number'] += $lastMonthNumber;
                }

                // Merge payment histories
                $updatedPaymentHistory = array_merge($paymentHistory, $paymentPeriods);

                $existingSubscription->update([
                    'total_amount' => $newTotal,
                    'balance' => $existingSubscription->balance + $totalAmount,
                    'payment_history' => $updatedPaymentHistory,
                    'number_of_payments' => $existingSubscription->number_of_payments + $numberOfPayments
                ]);

                // Record the additional payment with proper reference
                $reference = $paymentMethod . '-' . uniqid();
                $existingSubscription->recordPayment(
                    $totalAmount,
                    $reference
                );

                return $existingSubscription;
            }

        // Create new subscription
       // FIX 2 & 3: Create new subscription with proper data and payment history
       $subscription = new StudentRecurringSubscription([
            'student_id' => $student->id,
            'recurring_payment_plan_id' => $planId,
            'amount_per_month' => $amountPerMonth, // Fixed: Store the monthly amount
            'total_amount' => $totalAmount,
            'number_of_payments' => $numberOfPayments, // Fixed: Store number of payments
            'payment_method' => $paymentMethod,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_history' => $paymentPeriods, // Fixed: Store the payment periods
            'is_active' => true,
            'balance' => $totalAmount // Initialize balance to the total amount
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
        $this->recordSubscriptionPayment($subscription, $amount, $reference);

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

    protected function recordSubscriptionPayment($subscription, $amount, $reference)
    {
        // Get current values
        $currentPaid = $subscription->amount_paid;
        $newPaid = $currentPaid + $amount;
        $newBalance = $subscription->total_amount - $newPaid;

        // Get payment history
        $paymentHistory = $subscription->payment_history ?? [];

        // Calculate how many months this payment covers
        $amountPerMonth = $subscription->amount_per_month;
        $monthsCovered = floor($amount / $amountPerMonth);
        $remainingAmount = $amount;

        // Update payment history for covered months
        $updatedHistory = [];
        $monthsMarkedPaid = 0;

        foreach ($paymentHistory as $entry) {
            // If we still have funds and entry is not paid yet
            if ($remainingAmount >= $amountPerMonth && !$entry['paid'] && $monthsMarkedPaid < $monthsCovered) {
                $entry['paid'] = true;
                $entry['payment_date'] = Carbon::now()->format('Y-m-d');
                $entry['payment_reference'] = $reference . '-' . ($monthsMarkedPaid + 1);
                $remainingAmount -= $amountPerMonth;
                $monthsMarkedPaid++;
            }
            $updatedHistory[] = $entry;
        }

        // Update the subscription
        $subscription->update([
            'amount_paid' => $newPaid,
            'balance' => $newBalance,
            'payment_history' => $updatedHistory
        ]);

        return true;
    }

}
