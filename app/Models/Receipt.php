<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_id',
        'receipt_number',
        'amount',
        'date',
        'is_installment',
        'installment_number',
        'total_amount',
        'remaining_amount',
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_installment' => 'boolean',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }
}
