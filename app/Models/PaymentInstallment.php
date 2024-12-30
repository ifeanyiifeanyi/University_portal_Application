<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_id',
        'amount',
        'due_date',
        'paid_amount',
        'status',
        'installment_number',
        'paid_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];
    // PaymentInstallment Model Method
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', now());
    }

    public function updateStatus()
    {
        if ($this->status !== 'paid') {
            if ($this->due_date < now()) {
                $this->status = 'overdue';
            } else {
                $this->status = 'pending';
            }
            $this->save();
        }
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function calculatePenalty()
    {
        if ($this->status !== 'pending' || now()->lt($this->due_date)) {
            return 0;
        }

        $config = $this->payment->paymentType->installmentConfig;

        if ($config->late_fee_type === 'fixed') {
            return $config->late_fee_amount;
        }

        return ($this->amount * $config->late_fee_amount) / 100;
    }
}
