<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class RecurringPaymentReportController extends Controller
{
    public function getRecurringPayments(Request $request)
    {
        $year = $request->input('year', now()->year); // Default to current year

        $students = Student::with('recurringSubscriptions')
            ->get()
            ->map(function ($student) use ($year) {
                return $student->getPaymentsByYear($year);
            })
            ->flatten(1)
            ->filter(function ($payment) {
                return $payment['number_of_months'] > 0;
            });

        $yearlyTotals = $this->calculateYearlyTotals();

        return view('admin.payments.recurring_payment.paid', compact('students', 'year', 'yearlyTotals'));
    }

    public function studentRecurPayment($subscription_id)
    {
        // This method remains unchanged from your original intent
        // Add your implementation here if needed
    }

    public function exportYearlyPayments(Request $request)
    {
        $year = $request->input('year', now()->year);

        $students = Student::with('recurringSubscriptions')
            ->get()
            ->map(function ($student) use ($year) {
                return $student->getPaymentsByYear($year);
            })
            ->flatten(1)
            ->filter(function ($payment) {
                return $payment['number_of_months'] > 0;
            });

        $yearlyTotals = $this->calculateYearlyTotals();

        $pdf = Pdf::loadView('admin.payments.recurring_payment.pdf', [
            'students' => $students,
            'year' => $year,
            'yearlyTotals' => $yearlyTotals,
            'date' => now()->format('d M Y')
        ]);

        return $pdf->download("recurring_payments_{$year}.pdf");
    }

    private function calculateYearlyTotals()
    {
        return Student::with('recurringSubscriptions')
            ->get()
            ->flatMap(function ($student) {
                return $student->recurringSubscriptions;
            })
            ->groupBy(function ($subscription) {
                return Carbon::parse($subscription->created_at)->year;
            })
            ->map(function ($subscriptions, $year) {
                return [
                    'year' => $year,
                    'total' => $subscriptions->sum('amount_paid')
                ];
            })
            ->sortByDesc('year')
            ->values();
    }

    public function exportYearlyPaymentsCSV(Request $request)
    {
        $year = $request->input('year', now()->year);

        $students = Student::with('recurringSubscriptions')
            ->get()
            ->map(function ($student) use ($year) {
                return $student->getPaymentsByYear($year);
            })
            ->flatten(1)
            ->filter(function ($payment) {
                return $payment['number_of_months'] > 0;
            });

        // Create CSV content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=recurring_payments_{$year}.csv",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'SN',
                'Student Name',
                'Amount Paid (₦)',
                'Number of Months',
                'Payment Date',
                'Covered Months'
            ]);

            // Add data rows
            foreach ($students as $index => $payment) {
                $coveredMonths = collect($payment['months_list'])
                    ->map(function ($month) {
                        return "{$month['name']} {$month['year']}";
                    })
                    ->implode(', ');

                fputcsv($file, [
                    $index + 1,
                    $payment['student_name'],
                    number_format($payment['amount_paid'], 2),
                    $payment['number_of_months'],
                    Carbon::parse($payment['payment_date'])->format('d M Y'),
                    $coveredMonths
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
