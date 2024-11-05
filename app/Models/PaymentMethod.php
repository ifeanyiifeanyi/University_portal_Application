<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active', 'config', 'description', 'logo'];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function proveOfPayment(){
        return $this->hasMany(ProveOfPayment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function isCreditCard()
    {
        return strtolower($this->config['payment_type'] ?? '') === 'credit_card';
    }

}
