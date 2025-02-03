<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentType extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'amount',
        'description',
        'is_active',
        'academic_session_id',
        'semester_id',
        'slug',
        'payment_period', // 'semester' or 'session'
        'due_date',
        'late_fee_amount',
        'late_fee_type', // 'fixed' or 'percentage'
        'grace_period_days',
        'is_recurring',
        'paystack_subaccount_code',
        'subaccount_percentage'
    ];

    // In PaymentType model
    public function getFormattedDepartmentsAndLevels()
    {
        return $this->departments->map(function ($department) {
            return sprintf(
                '%s (Level: %s)',
                $department->name,
                $department->getDisplayLevel($department->pivot->level)
            );
        })->implode('&#13;');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class)->withPivot('level');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function proveOfPayment()
    {
        return $this->hasMany(ProveOfPayment::class);
    }
   
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function getAmount($departmentId, $level)
    {
        $pivot = $this->departments()
            ->where('department_id', $departmentId)
            ->where('level', $level)
            ->first();

        // If the relationship exists, return the payment type's amount
        if ($pivot) {
            return $this->amount; // Using the amount from payment_types table
            Log::info('Payment Type Amount:', [
                'payment_type_id' => $this->id,
                'amount' => $this->amount,
            ]);
        }

        return null;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    protected $casts = [
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
        'due_date' => 'date',
        'grace_period_days' => 'integer',
    ];

    public function departmentPaymentType()
    {
        return $this->hasMany(DepartmentPaymentType::class);
    }


    // method to calculate late fee
    public function calculateLateFee($paymentDate)
    {
        if (!$this->due_date || $paymentDate <= $this->due_date) {
            return 0;
        }

        // Check if still within grace period
        $gracePeriodEnd = $this->due_date->addDays($this->grace_period_days);
        if ($paymentDate <= $gracePeriodEnd) {
            return 0;
        }

        if ($this->late_fee_type === 'fixed') {
            return $this->late_fee_amount;
        }

        // Percentage calculation
        return ($this->amount * $this->late_fee_amount) / 100;
    }

    // method to check if payment is late
    public function isLate($date = null)
    {
        $checkDate = $date ?? now();
        $gracePeriodEnd = $this->due_date->addDays($this->grace_period_days);
        return $checkDate > $gracePeriodEnd;
    }



    // there below are relationships for installment payments & configurations
    public function installmentConfig()
    {
        return $this->hasOne(PaymentInstallmentConfig::class);
    }

    public function supportsInstallments()
    {
        return $this->installmentConfig && $this->installmentConfig->is_active;
    }
}
