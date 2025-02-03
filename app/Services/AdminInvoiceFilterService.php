<?php

namespace App\Services;

use App\Models\Invoice;

class AdminInvoiceFilterService
{
    /**
     * Create a new class instance.
     */
    protected $query;
    public function __construct()
    {
        $this->query = Invoice::query();
    }

    public function filters(array $filters)
    {
        $this->query->with([
            'academicSession',
            'semester',
            'student.user',
            'department',
            'paymentMethod',
            'paymentType',
            'payment',
        ]);

        $this->filterBySession($filters['session_id'] ?? null)
             ->filterBySemester($filters['semester_id'] ?? null)
             ->filterByStudentName($filters['student_name'] ?? null)
             ->filterByDateRange(
                 $filters['start_date'] ?? null,
                 $filters['end_date'] ?? null
             )
             ->filterByStatus($filters['status'] ?? null)
             ->filterByPaymentType($filters['payment_type_id'] ?? null);

        if (!isset($filters['show_archived'])) {
            $this->query->whereNull('archived_at');
        }

        return $this->query->latest()->get();
    }


    private function filterBySession(?int $sessionId): self{
        if ($sessionId) {
            $this->query->where('academic_session_id', $sessionId);
        }
        return $this;
    }

    private function filterBySemester($semesterId): self{
        if ($semesterId) {
            $this->query->where('semester_id', $semesterId);
        }
        return $this;
    }

    private function filterByStudentName($studentName): self{
        if ($studentName) {
            $this->query->whereHas('student.user', function ($query) use ($studentName) {
                $query->where('full_name', 'like', '%' . $studentName . '%');
            });
        }
        return $this;
    }

    private function filterByDateRange($startDate, $endDate): self{
        if ($startDate && $endDate) {
            $this->query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $this->query->whereDate('invoices.created_at', $startDate);
        } elseif ($endDate) {
            $this->query->whereDate('invoices.created_at', $endDate);
        }
        return $this;
    }

    private function filterByStatus(?string $status): self{
        if ($status) {
            $this->query->where('status', $status);
        }
        return $this;
    }

    private function filterByPaymentType(?int $paymentTypeId): self{
        if ($paymentTypeId) {
            $this->query->where('payment_type_id', $paymentTypeId);
        }
        return $this;
    }
    public function getTotalAmount(): float
    {
        return $this->query->whereIn('status', ['paid', 'partial'])->sum('amount');
    }

}
