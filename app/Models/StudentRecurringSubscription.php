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
