<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProveOfPayment extends Model
{
    use HasFactory;
    protected $fillable = [

        'payment_type_id',
        'payment_method_id',
        'amount',
        'transaction_reference',
        'bank_name',
        'proof_file',
        'additional_notes',
        'metadata',
        'status',
        'verified_by',
        'verified_at',
        'invoice_id'
    ];

    protected $casts = [
        'metadata' => 'array', // Automatically cast JSON metadata to array
        'processed_at' => 'datetime',
    ];

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'verified_by', 'verified_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
