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
        'penalty_amount',
        'paid_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2'
    ];

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
