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

    ];

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
