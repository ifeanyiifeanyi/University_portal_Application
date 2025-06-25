<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Student;
use App\Models\RecurringPaymentPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StudentRecurringSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'recurring_payment_plan_id',
        'amount_per_month',
        'total_amount',      // Total amount student needs to pay
        'amount_paid',       // Amount paid so far
        'balance',           // Remaining balance
        'start_date',        // When the subscription started
        'end_date',          // When the subscription ends
        'is_active',
        'payment_history'    // Record of all payments made
    ];

    public function getPercentagePaidAttribute()
    {
        if ($this->total_amount <= 0) {
            return 0;
        }

        $percentage = ($this->amount_paid / $this->total_amount) * 100;
        return round($percentage, 2);
    }

    public function getPaymentHistoryArrayAttribute()
    {
        $history = $this->payment_history;

        if (is_string($history)) {
            return json_decode($history, true) ?? [];
        }

        return is_array($history) ? $history : [];
    }
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_history' => 'array',
        'is_active' => 'boolean'
    ];

    protected $appends = ['status', 'percentage_paid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            // Set initial values if not already set
            if (!isset($subscription->amount_paid)) {
                $subscription->amount_paid = 0;
            }

            if (!isset($subscription->balance)) {
                $subscription->balance = $subscription->total_amount;
            }
        });
    }


    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function plan()
    {
        return $this->belongsTo(RecurringPaymentPlan::class, 'recurring_payment_plan_id');
    }

    // Status checks
    public function isPaid()
    {
        return $this->balance <= 0;
    }

    // Payment handling
    public function recordPayment($amount, $reference)
    {
        // Get current values
        $currentPaid = $this->amount_paid;
        $newPaid = $currentPaid + $amount;
        $newBalance = $this->total_amount - $newPaid;

        // Get payment history
        $paymentHistory = $this->payment_history ?? [];

        // Calculate how many months this payment covers
        $amountPerMonth = $this->amount_per_month;
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
        $this->update([
            'amount_paid' => $newPaid,
            'balance' => $newBalance,
            'payment_history' => $updatedHistory
        ]);

        return true;
    }

    public function recordPaymentForBlade($amount, $reference = null, $selectedMonths = null)
    {
        // Update subscription payment details
        $this->amount_paid += $amount;
        $this->balance = $this->total_amount - $this->amount_paid;
        $this->number_of_payments = floor($this->amount_paid / ($this->amount_per_month ?? 35000));

        // Add to payment history
        $history = $this->payment_history ?? [];
        $history[] = [
            'date' => now()->toDateString(),
            'amount' => $amount,
            'reference' => $reference
        ];
        $this->payment_history = $history;

        // Update selected_months if provided, or generate based on payment
        if ($selectedMonths) {
            // If specific months are provided (e.g., [1, 2, 3] for Jan, Feb, Mar)
            $this->selected_months = $selectedMonths;
        } else {
            // Automatically assign months based on start_date and number of payments
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $months = [];
            for ($i = 0; $i < $this->number_of_payments; $i++) {
                $monthDate = $startDate->copy()->addMonths($i);
                $months[] = $monthDate->month; // Store month number (1-12)
            }
            $this->selected_months = $months;
        }

        $this->save();
    }

    // In StudentRecurringSubscription model
    public function calculatePaidMonths($monthlyFee)
    {
        $paymentHistory = $this->payment_history ?? [];
        $months = [];
        $totalPaid = collect($paymentHistory)->sum('amount');
        $monthsCount = floor($totalPaid / $monthlyFee);

        if ($monthsCount <= 0) {
            return [
                'months_count' => 0,
                'months' => []
            ];
        }

        $startDate = \Carbon\Carbon::parse($this->start_date);
        for ($i = 0; $i < $monthsCount; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            $months[] = [
                'name' => $monthDate->format('F'),
                'year' => $monthDate->year
            ];
        }

        return [
            'months_count' => $monthsCount,
            'months' => $months
        ];
    }

    public function calculatePaidMonthsFromSelected()
    {
        $selectedMonths = $this->selected_months ?? [];
        $paymentHistory = $this->payment_history ?? [];
        $monthlyFee = $this->amount_per_month ?? 35000;
        $startDate = \Carbon\Carbon::parse($this->start_date);

        if (empty($selectedMonths)) {
            return $this->calculatePaidMonths($monthlyFee);
        }

        $totalPaid = collect($paymentHistory)->sum('amount');
        $monthsPaidCount = floor($totalPaid / $monthlyFee);

        $paidMonths = collect($selectedMonths)->take($monthsPaidCount)->map(function ($monthNumber) use ($startDate) {
            $monthDate = $startDate->copy()->month($monthNumber)->startOfMonth();
            return [
                'name' => $monthDate->format('F'),
                'year' => $monthDate->year,
                'full_date' => $monthDate->format('Y-m-d')
            ];
        })->toArray();

        return [
            'months' => $paidMonths,
            'total_months_paid' => count($paidMonths),
            'remaining_amount' => $totalPaid % $monthlyFee
        ];
    }

    // Get payment status
    // public function getStatusAttribute()
    // {
    //     if (!$this->is_active) return 'Inactive';
    //     if ($this->isPaid()) return 'Paid';
    //     if ($this->balance > 0) return 'Pending';
    //     return 'Active';
    // }

    public function getStatusAttribute()
    {
        if ($this->balance <= 0) {
            return 'completed';
        }

        if ($this->amount_paid > 0) {
            return 'partial';
        }

        return 'pending';
    }

    /**
     * Get the covered months as a formatted string.
     */
    public function getCoveredMonthsAttribute()
    {
        $paymentHistory = $this->payment_history ?? [];
        $paidMonths = [];

        foreach ($paymentHistory as $entry) {
            if ($entry['paid']) {
                $paidMonths[] = $entry['period'];
            }
        }

        return implode(', ', $paidMonths);
    }

    /**
     * Get the remaining months as a formatted string.
     */
    public function getRemainingMonthsAttribute()
    {
        $paymentHistory = $this->payment_history ?? [];
        $unpaidMonths = [];

        foreach ($paymentHistory as $entry) {
            if (!$entry['paid']) {
                $unpaidMonths[] = $entry['period'];
            }
        }

        return implode(', ', $unpaidMonths);
    }

    // Boot method for model events
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($subscription) {
    //         // Set initial values if not set
    //         if (!isset($subscription->amount_paid)) {
    //             $subscription->amount_paid = 0;
    //         }
    //         if (!isset($subscription->balance)) {
    //             $subscription->balance = $subscription->total_amount;
    //         }
    //         if (!isset($subscription->start_date)) {
    //             $subscription->start_date = now();
    //         }
    //     });
    // }
}
