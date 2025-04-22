<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use App\Models\ScoreAudit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PharIo\Manifest\Exception;
use App\Imports\StudentsImport;
use App\Models\AcademicSession;
use App\Services\StudentService;
use App\Jobs\ProcessStudentImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\StudentBatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Exports\StudentTemplateExport;
use App\Models\SemesterCourseRegistration;
use WisdomDiala\Countrypkg\Models\Country;
use App\Http\Requests\CreateNewStudentRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateStudentDataRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminStudentController extends Controller
{
    protected $studentBatchService;
    protected $studentService;

    public function __construct(StudentBatchService $studentBatchService, StudentService $studentService)
    {
        $this->studentBatchService = $studentBatchService;
        $this->studentService = $studentService;
    }
    public function index()
    {
        $studentsWithUsers = Student::with(['user', 'department'])->latest()->get();

        // Filter out students with null users
        $students = $studentsWithUsers->filter(function ($student) {
            return $student->user !== null;
        });

        $departments = Department::all();

        // Get analytics data
        $studentStats = $this->getStudentStats($students);
        $chartData = $this->getChartData($students, $departments);

        return view('admin.student.index', compact('students', 'departments', 'studentStats', 'chartData'));
    }


    /**
     * Generate statistics for student dashboard
     */
    private function getStudentStats($students)
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        return [
            'total' => $students->count(),
            'active' => $students->count(), // Assuming all returned students are active
            'newThisMonth' => $students->filter(function ($student) use ($startOfMonth) {
                return $student->created_at >= $startOfMonth;
            })->count(),
            'departments' => Department::count()
        ];
    }

    /**
     * Generate chart data for student analytics
     */
    private function getChartData($students, $departments)
    {
        // Department distribution
        $departmentCounts = [];
        $departmentLabels = [];

        foreach ($departments as $department) {
            $count = $students->where('department_id', $department->id)->count();
            if ($count > 0) {
                $departmentLabels[] = $department->name;
                $departmentCounts[] = $count;
            }
        }

        // Level distribution
        $levelCounts = [];
        $levelLabels = [];

        $studentsByLevel = $students->groupBy('current_level');
        foreach ($studentsByLevel as $level => $levelStudents) {
            // Try to get a display format for the level
            $displayLevel = $level;
            if ($levelStudents->first() && $levelStudents->first()->department) {
                $displayLevel = $levelStudents->first()->department->getDisplayLevel($level);
            }

            $levelLabels[] = $displayLevel;
            $levelCounts[] = $levelStudents->count();
        }

        // Admission trends (past 5 years)
        $currentYear = now()->year;
        $admissionYears = [];
        $admissionCounts = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = $currentYear - $i;
            $admissionYears[] = $year;
            $admissionCounts[] = $students->where('year_of_admission', $year)->count();
        }

        return [
            'departments' => [
                'labels' => $departmentLabels,
                'data' => $departmentCounts
            ],
            'levels' => [
                'labels' => $levelLabels,
                'data' => $levelCounts
            ],
            'admissionTrends' => [
                'labels' => $admissionYears,
                'data' => $admissionCounts
            ]
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        $departments = Department::query()->latest()->get();
        return view('admin.student.create', compact('departments', 'countries'));
    }

    // fetch the department academic levels

    public function levels(Department $department)
    {
        // Return levels in display format
        return response()->json($department->levels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewStudentRequest $request)
    {

        try {
            $student = $this->studentService->createStudent($request->validated());

            $notification = [
                'message' => 'Student account created successfully.',
                'alert-type' => 'success'
            ];

            return redirect()->route('admin.student.view')->with($notification);
        } catch (\Exception $e) {
            $notification = [
                'message' => 'An error occurred while creating the student account. Please try again.' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            return back()->with($notification);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return view('admin.student.details', compact('student'));
    }



    /**
     * Show the form for editing the specified resource.
     */

    public function getDepartment(Department $department)
    {
        $levels = $department->levels;
        $levelMap = [];

        foreach ($levels as $level) {
            $levelMap[$level] = $department->getLevelNumber($level);
        }

        return response()->json([
            'levels' => $levels,
            'levelMap' => $levelMap,
            'level_format' => $department->level_format,
            'duration' => $department->duration
        ]);
    }
    public function edit(Student $student)
    {
        $countries = Country::all();
        $departments = Department::all();
        $currentDepartment = Department::find($student->department_id);
        $levels = $currentDepartment ? $currentDepartment->levels : [];

        return view('admin.student.edit', compact(
            'student',
            'departments',
            'countries',
            'levels',
            'currentDepartment'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentDataRequest $request, Student $student): RedirectResponse
    {
        $result = $this->studentService->updateFromAdmin($request, $student);

        return redirect()->route('admin.student.view')
            ->with($result);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $student->delete();  // This will soft delete the student
            $student->user->delete();  // This will soft delete the associated user
        });

        $notification = [
            'message' => 'Student account deleted successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.student.view')->with($notification);
    }


    public function studentRegistrationHistory($studentId)
    {
        $student = Student::findOrFail($studentId);

        $registrationHistory = SemesterCourseRegistration::where('student_id', $studentId)
            ->with(['academicSession', 'semester', 'courseEnrollments.course', 'courseEnrollments'])
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get();

        return view('admin.student.registration_history', compact('student', 'registrationHistory'));
    }


    public function viewApprovedScoreHistory(Student $student)
    {
        $scores = $student->scores()
            ->where('status', 'approved')
            ->with(['course', 'teacher', 'scoreAudits', 'academicSession', 'semester'])
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get()
            ->groupBy(function ($score) {
                return $score->academicSession->name . ' - ' . $score->semester->name;
            });

        return view('admin.student.score_approval_score_history', compact('scores', 'student'));
    }





    public function viewAudits(Student $student, Request $request)
    {
        $query = $this->buildAuditQuery($student);

        // Apply search filters
        $query = $this->applySearchFilters($query, $request);

        $groupedAudits = $query->get()->groupBy(['session_name', 'semester_name', 'course_title']);

        // Get unique values for dropdowns
        $academicSessions = AcademicSession::pluck('name', 'id');
        $semesters = Semester::pluck('name', 'id');
        $courses = Course::pluck('title', 'id');

        return view('admin.student.audit', compact('student', 'groupedAudits', 'academicSessions', 'semesters', 'courses'));
    }

    private function buildAuditQuery($student)
    {
        return ScoreAudit::join('student_scores', 'score_audits.student_score_id', '=', 'student_scores.id')
            ->join('academic_sessions', 'student_scores.academic_session_id', '=', 'academic_sessions.id')
            ->join('semesters', 'student_scores.semester_id', '=', 'semesters.id')
            ->join('courses', 'student_scores.course_id', '=', 'courses.id')
            ->join('teachers', 'student_scores.teacher_id', '=', 'teachers.id')
            ->join('users as teachers_users', 'teachers.user_id', '=', 'teachers_users.id')
            ->join('users as students_users', 'student_scores.student_id', '=', 'students_users.id')
            ->where('student_scores.student_id', $student->id)
            ->select(
                'score_audits.*',
                'academic_sessions.name as session_name',
                'semesters.name as semester_name',
                'courses.title as course_title',
                DB::raw("CONCAT(teachers_users.first_name, ' ', teachers_users.last_name, ' ', COALESCE(teachers_users.other_name, '')) as teacher_name"),
                DB::raw("CONCAT(students_users.first_name, ' ', students_users.last_name, ' ', COALESCE(students_users.other_name, '')) as student_name")
            )
            ->orderBy('academic_sessions.name', 'desc')
            ->orderBy('semesters.name', 'asc')
            ->orderBy('courses.title', 'asc');
    }

    private function applySearchFilters($query, $request)
    {
        if ($request->filled('academic_session')) {
            $query->where('academic_sessions.id', $request->academic_session);
        }
        if ($request->filled('semester')) {
            $query->where('semesters.id', $request->semester);
        }
        if ($request->filled('course')) {
            $query->where('courses.id', $request->course);
        }
        if ($request->filled('student_name')) {
            $query->where(DB::raw("CONCAT(students_users.first_name, ' ', students_users.last_name, ' ', COALESCE(students_users.other_name, ''))"), 'LIKE', '%' . $request->student_name . '%');
        }
        if ($request->filled('teacher_name')) {
            $query->where(DB::raw("CONCAT(teachers_users.first_name, ' ', teachers_users.last_name, ' ', COALESCE(teachers_users.other_name, ''))"), 'LIKE', '%' . $request->teacher_name . '%');
        }
        return $query;
    }

    /**
     * Download the student import template in Excel and PDF format
     *
     * @return BinaryFileResponse
     */

    // Params: $format = 'excel' || 'pdf'
    public function downloadTemplate($format = 'excel')
    {
        if ($format === 'pdf') {
            $path = public_path('templates/CONSCO.pdf');

            if (!file_exists($path)) {
                return back()->with('error', 'PDF template file not found.');
            }

            return response()->download($path, 'CONSCO.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="CONSCO.pdf"'
            ]);
        }

        return Excel::download(new StudentTemplateExport, 'student_import_template.xlsx');
    }


    // verify the uploaded file contents in form format
    public function importVerify(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'file' => 'required|file|mimes:xlsx,xls',
        ]);




        $filePath = $request->file('file')->store('imports');

        $students = Excel::toCollection(new StudentsImport($request->department_id), storage_path('app/' . $filePath))->first();

        return view('admin.student.import-student', [
            'department_id' => $request->department_id,
            'filePath' => $filePath,
            'students' => $students,
        ]);
    }




    public function importProcess(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'students' => 'required|array'
            ]);

            // Log the initial request
            Log::info('Starting student import process', [
                'total_records' => count($request->students),
                'department_id' => $request->department_id
            ]);

            // Generate a unique batch ID
            $batchId = uniqid('batch_', true);

            // Dispatch the job with a smaller chunk size
            $chunks = array_chunk($request->students, 25); // Process 25 students at a time

            foreach ($chunks as $index => $chunk) {
                ProcessStudentImport::dispatch($chunk, $request->department_id, $batchId)
                    ->delay(now()->addSeconds($index * 30)); // Add delay between chunks
            }

            return redirect()
                ->route('admin.student.import-status')
                ->with('message', 'Student import has been queued for processing. You can check the status on this page.');
        } catch (Exception $e) {
            Log::error('Failed to queue student import', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to queue student import: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // New method to check import status
    public function importStatus()
    {
        $batchId = session('latest_import_batch');
        $results = Cache::get("student_import_{$batchId}");
        if (!$results) {
            return redirect()->route('admin.student.view')
                ->with('warning', 'No active import process found.');
        }

        return view('admin.student.import-status', [
            'batchId' => $batchId,
            'results' => $results
        ]);
    }

    public function generateIdCard(Student $student)
    {
        return view('admin.student.id-card', compact('student'));
    }
}
