<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallmentConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_type_id',
        'number_of_installments',
        'minimum_first_payment_percentage',
        'interval_days',
        'late_fee_amount',
        'late_fee_type',
        'is_active'
    ];

    protected $casts = [
        'payment_type_id' => 'integer',
        'number_of_installments' => 'integer',
        'minimum_first_payment_percentage' => 'decimal:2',
        'interval_days' => 'integer',
        'late_fee_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
