<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use App\Services\StudentOwedFeeService;

class AdminStudentFeeNotPaidController extends Controller
{
    public function __construct(private StudentOwedFeeService $studentOwedFeeService) {}


    public function index(Request $request)
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        // Override current session/semester if selected in filters
        if ($request->filled('session')) {
            $currentSession = AcademicSession::find($request->session);
        }

        if ($request->filled('semester')) {
            $currentSemester = Semester::find($request->semester);
        }

        $result = $this->studentOwedFeeService->getDefaulters($request, $currentSession, $currentSemester);

        return view('admin.payments.owingStudent.index', [
            'students' => $result['students'],
            'departments' => $result['filters']['departments'],
            'sessions' => $result['filters']['sessions'],
            'semesters' => $result['filters']['semesters'],
            'statistics' => $result['statistics'],
            'currentSession' => $result['current']['session'],
            'currentSemester' => $result['current']['semester']
        ]);
    }

    public function export(Request $request)
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $result = $this->studentOwedFeeService->getDefaulters($request, $currentSession, $currentSemester);
        $csv = $this->studentOwedFeeService->exportToCsv($result['students']);

        $filename = 'defaulters_' . date('Y-m-d') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    public function print(Request $request)
    {
        $currentSession = AcademicSession::find($request->session) ?? AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::find($request->semester) ?? Semester::where('is_current', true)->first();

        $result = $this->studentOwedFeeService->getDefaulters($request, $currentSession, $currentSemester);

        return view('admin.payments.owingStudent.print', [
            'students' => $result['students'],
            'currentSession' => $result['current']['session'],
            'currentSemester' => $result['current']['semester']
        ]);
    }
}
