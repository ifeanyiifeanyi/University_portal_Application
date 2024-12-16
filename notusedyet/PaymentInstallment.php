<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_id',
        'name',
        'payment_type_id',
        'payment_method_id',
        'payment_schedule_id',
        'amount',
        'due_date',
        'penalty_amount',
        'status',
        'installment_number',
    ];

    public function calculatePenalty()
    {
        if ($this->due_date->isPast() && $this->status !== 'paid') {
            $daysLate = now()->diffInDays($this->due_date);
            $schedule = $this->payment->paymentSchedule;
            $penaltyPercentage = $schedule->late_penalty_percentage;

            return ($this->amount * ($penaltyPercentage / 100)) * ceil($daysLate / 7); // Weekly penalty
        }
        return 0;
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentSchedule()
    {
        return $this->belongsTo(PaymentSchedule::class);
    }
}
