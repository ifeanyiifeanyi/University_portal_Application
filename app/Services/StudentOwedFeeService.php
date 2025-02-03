<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use Illuminate\Support\Collection;

class StudentOwedFeeService
{
    public function getDefaulters(Request $request, ?AcademicSession $currentSession = null, ?Semester $currentSemester = null): array
    {
        $currentSession = $currentSession ?? AcademicSession::where('is_current', true)->first();
        $currentSemester = $currentSemester ?? Semester::where('is_current', true)->first();

        $query = $this->buildDefaultersQuery($request, $currentSession, $currentSemester);
        $students = $query->get();

        $this->attachPaymentDetails($students, $currentSession, $currentSemester);

        return [
            'students' => $students,
            'statistics' => $this->generateStatistics($students, Department::all()),
            'filters' => $this->getFilterData(),
            'current' => [
                'session' => $currentSession,
                'semester' => $currentSemester
            ]
        ];
    }

    private function attachPaymentDetails(Collection $students, AcademicSession $session, Semester $semester)
    {
        foreach ($students as $student) {
            // Get payment types through the pivot relationship
            $paymentTypes = PaymentType::whereHas('departments', function ($query) use ($student) {
                $query->where('departments.id', $student->department_id)
                    ->where('department_payment_type.level', $student->current_level);
            })
            ->where('semester_id', $semester->id)
            ->get();

            $totalOwed = 0;
            $paymentBreakdown = [];

            foreach ($paymentTypes as $paymentType) {
                // Check if payment exists
                $paid = $student->payments()
                    ->where('payment_type_id', $paymentType->id)
                    ->where('academic_session_id', $session->id)
                    ->where('semester_id', $semester->id)
                    ->where('status', 'paid')
                    ->exists();

                if (!$paid) {
                    // Get amount from pivot table if it exists, otherwise use default amount
                    $amount = $paymentType->departments()
                        ->where('departments.id', $student->department_id)
                        ->where('department_payment_type.level', $student->current_level)
                        ->first()
                        ->pivot
                        ->amount ?? $paymentType->amount;

                    $totalOwed += $amount;
                    $paymentBreakdown[] = [
                        'name' => $paymentType->name,
                        'amount' => $amount,
                        'due_date' => $paymentType->due_date,
                        'is_mandatory' => $paymentType->is_mandatory,
                    ];
                }
            }

            $student->total_owed = $totalOwed;
            $student->payment_breakdown = $paymentBreakdown;
        }
    }

    private function buildDefaultersQuery(Request $request, AcademicSession $currentSession, Semester $currentSemester)
    {
        $query = Student::query()
            ->with(['department', 'user'])
            ->whereDoesntHave('payments', function ($query) use ($currentSession, $currentSemester) {
                $query->where('academic_session_id', $currentSession->id)
                    ->where('semester_id', $currentSemester->id)
                    ->where('status', 'paid')
                    ->orWhere('status', 'partial');
            });

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('level')) {
            $query->where('current_level', $request->level);
        }

        return $query;
    }

    private function generateStatistics(Collection $students, Collection $departments): array
    {
        $departmentStats = $students->groupBy('department_id')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total_owed' => $group->sum('total_owed')
            ]);

        return [
            'total_defaulters' => $students->count(),
            'total_amount_owed' => $students->sum('total_owed'),
            'by_department' => $departments->whereIn('id', $departmentStats->keys())
                ->map(function ($department) use ($departmentStats) {
                    return [
                        'name' => $department->name,
                        'count' => $departmentStats[$department->id]['count'] ?? 0,
                        'total_owed' => $departmentStats[$department->id]['total_owed'] ?? 0
                    ];
                })
                ->values()
                ->toArray(),
            'by_level' => $students->groupBy('current_level')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'total_owed' => $group->sum('total_owed')
                ])
                ->toArray()
        ];
    }

    private function getFilterData(): array
    {
        return [
            'departments' => Department::all(),
            'sessions' => AcademicSession::all(),
            'semesters' => Semester::all(),
        ];
    }

    public function exportToCsv(Collection $students): string
    {
        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, [
            'Student Name',
            'Matric Number',
            'Department',
            'Level',
            'Email',
            'Total Amount Owed',
            'Payment Types Due',
            'Due Dates'
        ]);

        foreach ($students as $student) {
            $paymentTypesList = collect($student->payment_breakdown)
                ->pluck('name')
                ->join(', ');

            $dueDatesList = collect($student->payment_breakdown)
                ->pluck('due_date')
                ->map(fn($date) => $date ? date('Y-m-d', strtotime($date)) : 'N/A')
                ->join(', ');

            fputcsv($handle, [
                $student->user->full_name,
                $student->matric_number,
                $student->department->name,
                $student->current_level,
                $student->user->email,
                number_format($student->total_owed, 2),
                $paymentTypesList,
                $dueDatesList
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
