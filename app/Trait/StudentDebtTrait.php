<?php

namespace App\Trait;



trait StudentDebtTrait
{
    public function getLevelCode()
    {
        // Use the department's getDisplayLevel method to convert numeric level
        // to the appropriate format (ND/HND/RN)
        return $this->department->getLevelNumber($this->current_level);
    }

    public function calculateDebt($session, $semester)
    {
        $levelCode = $this->getLevelCode();

        // Get all payment types for this department and level through the pivot
        $paymentTypes = \App\Models\PaymentType::whereHas('departments', function ($query) use ($levelCode) {
            $query->where('departments.id', $this->department_id)
                ->where(function ($q) use ($levelCode) {
                    $q->whereRaw('LOWER(department_payment_type.level) = ?', [strtolower($levelCode)])
                        // Also check numeric level as fallback
                        ->orWhere('department_payment_type.level', $this->current_level);
                });
        })
            ->where('semester_id', $semester->id)
            ->get();


        $totalOwed = 0;
        $paymentBreakdown = [];

        foreach ($paymentTypes as $paymentType) {
            // Check if payment exists
            $paid = $this->payments()
                ->where('payment_type_id', $paymentType->id)
                ->where('academic_session_id', $session->id)
                ->where('semester_id', $semester->id)
                ->where('status', 'paid')
                ->exists();

            if (!$paid) {
                // Get the specific amount for this department and level
                $departmentPayment = $paymentType->departments()
                    ->where('departments.id', $this->department_id)
                    ->where(function ($q) use ($levelCode) {
                        $q->whereRaw('LOWER(department_payment_type.level) = ?', [$levelCode])
                            // Also check numeric level as fallback
                            ->orWhere('department_payment_type.level', $this->current_level);
                    })
                    ->first();

                $amount = $departmentPayment?->pivot?->amount ?? $paymentType->amount;

                $totalOwed += $amount;
                $paymentBreakdown[] = [
                    'name' => $paymentType->name,
                    'amount' => $amount,
                    'due_date' => $paymentType->due_date,
                    'is_mandatory' => $paymentType->is_mandatory,
                ];
            }
        }

        return [
            'total' => $totalOwed,
            'breakdown' => $paymentBreakdown
        ];
    }

    public function getFormattedLevelAttribute()
    {
        return $this->getLevelCode();
    }
}
