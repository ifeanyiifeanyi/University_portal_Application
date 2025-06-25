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



    /**
 * Calculate which months have been paid for based on payment history
 */
    public function calculatePaidMonths($subscription, $monthlyFee)
    {
        $paymentHistory = $subscription->payment_history ?? [];
        $months = [];
        $totalPaid = 0;

        // Sort payments by date
        $sortedPayments = collect($paymentHistory)->sortBy('date');

        $startDate = \Carbon\Carbon::parse($subscription->start_date);
        $currentMonth = $startDate->copy();

        foreach ($sortedPayments as $payment) {
            $totalPaid += $payment['amount'];

            // Calculate how many months this payment covers
            $monthsCovered = floor($totalPaid / $monthlyFee);

            // Add months to the paid months array
            while (count($months) < $monthsCovered) {
                $months[] = [
                    'name' => $currentMonth->format('F'),
                    'year' => $currentMonth->format('Y'),
                    'full_date' => $currentMonth->format('Y-m-d')
                ];
                $currentMonth->addMonth();
            }

            // Subtract the months covered from total paid
            $totalPaid = $totalPaid % $monthlyFee;
        }

        return [
            'months' => $months,
            'total_months_paid' => count($months),
            'remaining_amount' => $totalPaid
        ];
    }

    /**
     * Alternative method if you want to use selected_months field
     */
    public function calculatePaidMonthsFromSelected($subscription)
    {
        $selectedMonths = $subscription->selected_months ?? [];
        $paymentHistory = $subscription->payment_history ?? [];

        if (empty($selectedMonths)) {
            return $this->calculatePaidMonths($subscription, $subscription->amount_per_month ?? 35000);
        }

        $paidMonths = [];
        $totalPaid = collect($paymentHistory)->sum('amount');
        $monthlyFee = $subscription->amount_per_month ?? 35000;
        $monthsPaidCount = floor($totalPaid / $monthlyFee);

        // Take the first N months from selected months based on payments made
        $paidMonths = collect($selectedMonths)->take($monthsPaidCount)->map(function($month) {
            $date = \Carbon\Carbon::parse($month);
            return [
                'name' => $date->format('F'),
                'year' => $date->format('Y'),
                'full_date' => $date->format('Y-m-d')
            ];
        })->toArray();

        return [
            'months' => $paidMonths,
            'total_months_paid' => count($paidMonths),
            'remaining_amount' => $totalPaid % $monthlyFee
        ];
    }
}
