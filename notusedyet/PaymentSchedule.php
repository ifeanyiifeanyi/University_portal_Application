<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_type_id',
        'academic_session_id',
        'semester_id',
        'due_date',
        'grace_period_days',
        'late_penalty_percentage',
        'minimum_first_installment_percentage',
        'maximum_installments',
        'installment_interval_days'
    ];

    protected $casts = [
        'due_date' => 'date',
        'late_penalty_percentage' => 'decimal:2',
        'minimum_first_installment_percentage' => 'decimal:2'
    ];

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }


    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    
}
