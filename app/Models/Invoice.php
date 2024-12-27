<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
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
        'archived_at',
        'deleted_at',
        'is_installment',
        'current_transaction_amount'
    ];
    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'archived_at'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Invoice has been {$eventName}")
            ->useLogName('invoice');
    }






    protected $casts = [
        'archived_at' => 'date',
        'deleted_at' => 'date',
        'is_installment' => 'boolean',
        'amount' => 'decimal:2',
        'current_transaction_amount' => 'decimal:2'
    ];

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('status', 'pending');
    }


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function proveOfPayment()
    {

        return $this->hasMany(ProveOfPayment::class);
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

    public function payment()
    {
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
