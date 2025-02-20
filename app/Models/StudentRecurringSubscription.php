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
        'is_active',
        'payment_history'    // Record of all payments made
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'start_date' => 'date',
        'is_active' => 'boolean',
        'payment_history' => 'array'
    ];

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
    public function recordPayment($amount, $reference = null)
    {
        // Update subscription payment details
        $this->amount_paid += $amount;
        $this->balance = $this->total_amount - $this->amount_paid;

        // Add to payment history
        $history = $this->payment_history ?? [];
        $history[] = [
            'date' => now()->toDateString(),
            'amount' => $amount,
            'reference' => $reference
        ];
        $this->payment_history = $history;

        $this->save();
    }

    // Get payment status
    public function getStatusAttribute()
    {
        if (!$this->is_active) return 'Inactive';
        if ($this->isPaid()) return 'Paid';
        if ($this->balance > 0) return 'Pending';
        return 'Active';
    }

    // Get percentage paid
    public function getPercentagePaidAttribute()
    {
        return ($this->amount_paid / $this->total_amount) * 100;
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            // Set initial values if not set
            if (!isset($subscription->amount_paid)) {
                $subscription->amount_paid = 0;
            }
            if (!isset($subscription->balance)) {
                $subscription->balance = $subscription->total_amount;
            }
            if (!isset($subscription->start_date)) {
                $subscription->start_date = now();
            }
        });
    }
}
