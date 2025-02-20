<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // installments configs and relations
    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function setupInstallments()
    {
        $config = $this->paymentType->installmentConfig;
        if (!$config) {
            throw new \Exception('Installment configuration not found for this payment type');
        }

        $totalAmount = $this->amount;
        $firstInstallmentAmount = ($totalAmount * $config->minimum_first_payment_percentage) / 100;
        $remainingAmount = $totalAmount - $firstInstallmentAmount;
        $remainingInstallments = $config->number_of_installments - 1;
        $regularInstallmentAmount = $remainingAmount / $remainingInstallments;

        // Create first installment
        $this->installments()->create([
            'amount' => $firstInstallmentAmount,
            'due_date' => now(),
            'installment_number' => 1,
            'status' => 'pending'
        ]);

        // Create remaining installments
        for ($i = 0; $i < $remainingInstallments; $i++) {
            $this->installments()->create([
                'amount' => $regularInstallmentAmount,
                'due_date' => now()->addDays(($i + 1) * $config->interval_days),
                'installment_number' => $i + 2,
                'status' => 'pending'
            ]);
        }
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'payment_date' => 'date',
        'is_manual' => 'boolean',
        'is_installment' => 'boolean',
        'base_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'next_installment_date' => 'date',
        'remaining_amount' => 'decimal:2',
        'next_transaction_amount' => 'decimal:2',

    ];
    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function proveOfPayment()
    {
        return $this->hasMany(ProveOfPayment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }


    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_number', 'invoice_number');
    }
}
