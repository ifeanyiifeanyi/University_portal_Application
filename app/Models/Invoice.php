<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'payment_type_id',
        'department_id',
        'level',
        'academic_session_id',
        'semester_id',
        'amount',
        'payment_method_id',
        'status',
        'invoice_number',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester()
    {
        
        return $this->belongsTo(Semester::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public static function findPendingInvoice($studentId, $paymentTypeId, $academicSessionId, $semesterId)
    {
        return self::where('student_id', $studentId)
            ->where('payment_type_id', $paymentTypeId)
            ->where('academic_session_id', $academicSessionId)
            ->where('semester_id', $semesterId)
            ->where('status', 'pending')
            ->first();
    }
}
