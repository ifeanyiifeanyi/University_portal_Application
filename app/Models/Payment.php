<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'department_id',
        'level',
        'status',
        'academic_session_id',
        'semester_id',
        'payment_type_id',
        'payment_method_id',
        'transaction_reference',
        'amount',
        'payment_date',
        'payment_proof',
        'admin_id',
        'admin_comment',
        'is_manual',
        'invoice_number',

        'base_amount',
        'late_fee',

        'payment_reference',
        'gateway_response',
        'payment_channel'

    ];

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    // public function setupInstallments($numberOfInstallments)
    // {
    //     $schedule = $this->paymentSchedule;
    //     $installmentAmount = $this->total_amount / $numberOfInstallments;
    //     $interval = $schedule->installment_interval_days;

    //     for ($i = 0; $i < $numberOfInstallments; $i++) {
    //         $dueDate = $schedule->due_date->copy()->addDays($i * $interval);

    //         // First installment has minimum requirement
    //         if ($i === 0) {
    //             $minAmount = $this->total_amount * ($schedule->minimum_first_installment_percentage / 100);
    //             $installmentAmount = max($installmentAmount, $minAmount);
    //         }

    //         $this->installments()->create([
    //             'amount' => $installmentAmount,
    //             'due_date' => $dueDate,
    //             'installment_number' => $i + 1,
    //             'status' => 'pending'
    //         ]);
    //     }
    // }
    public function updatePenalties()
    {
        $totalPenalty = 0;
        foreach ($this->installments as $installment) {
            $penalty = $installment->calculatePenalty();
            $installment->update(['penalty_amount' => $penalty]);
            $totalPenalty += $penalty;
        }

        $this->update(['total_penalty_amount' => $totalPenalty]);
        return $totalPenalty;
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'payment_date' => 'date',
        'is_manual' => 'boolean',

    ];
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
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

    // public function receipt(){
    //     return $this->hasOne(Receipt::class, 'payment_id');
    // }
}
