<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringPaymentPlan extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'amount', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(StudentRecurringSubscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get total subscriptions
    public function getSubscriptionsCountAttribute()
    {
        return $this->subscriptions()->count();
    }

    // Get active subscriptions
    public function getActiveSubscriptionsCountAttribute()
    {
        return $this->subscriptions()->where('is_active', true)->count();
    }

    // Calculate total revenue from this plan
    public function getTotalRevenueAttribute()
    {
        return $this->subscriptions()->sum('amount_paid');
    }

    // Get all payments for this plan
    public function getAllPayments()
    {
        return $this->subscriptions()
            ->with('student')
            ->get()
            ->flatMap(function ($subscription) {
                return collect($subscription->payment_history)->map(function ($payment) use ($subscription) {
                    return [
                        'student_name' => $subscription->student->name,
                        'student_id' => $subscription->student->id,
                        'amount' => $payment['amount'],
                        'date' => $payment['date'],
                        'reference' => $payment['reference'] ?? null
                    ];
                });
            });
    }
}
