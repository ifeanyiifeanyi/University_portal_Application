<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\CourseEnrollment;
use App\Http\Controllers\Controller;
use App\Models\SemesterCourseRegistration;

class AdminStudentRegisteredCoursesController extends Controller
{
    // public function index(Request $request)
    // {
    //     $academicSessions = AcademicSession::all();
    //     $semesters = Semester::all();
    //     $departments = Department::all();

    //     // search dynamically
    //     $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
    //     $currentSemester = Semester::where('is_current', true)->firstOrFail();

    //     $query = CourseEnrollment::with(['student', 'course', 'department']);

    //     // Filter by academic session if provided
    //     if ($request->has('academic_session_id')) {
    //         $query->where('academic_session_id', $request->input('academic_session_id'));
    //     } else {
    //         $query->where('academic_session_id', $currentAcademicSession->id);
    //     }

    //     // Filter by semester if provided
    //     if ($request->has('semester_id')) {
    //         $query->where('semester_id', $request->input('semester_id'));
    //     } else {
    //         $query->where('semester_id', $currentSemester->id);
    //     }

    //     // Filter by department
    //     if ($request->has('department_id')) {
    //         $query->where('department_id', $request->input('department_id'));
    //     }

    //     // Filter by department level
    //     if ($request->has('level')) {
    //         $query->where('level', $request->input('level'));
    //     }

    //     // Filter by date range
    //     if ($request->has('date_from') && $request->has('date_to')) {
    //         $query->whereBetween('registered_at', [$request->input('date_from'), $request->input('date_to')]);
    //     }

    //     $courseRegistrations = $query->get();



    //     return view('admin.all_course_registrations.index', compact('academicSessions', 'semesters', 'departments'));
    // }

    public function index(Request $request)
    {
        $query = SemesterCourseRegistration::with(['student', 'semester', 'academicSession', 'student.department', 'courseEnrollments']);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                })
                    ->orWhereHas('academicSession', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('semester', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $registrations = $query->paginate(200)->appends($request->all());

        $departments = Department::all();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();

        // Get statistics
        $stats = [
            'total' => SemesterCourseRegistration::count(),
            'pending' => SemesterCourseRegistration::where('status', 'pending')->count(),
            'approved' => SemesterCourseRegistration::where('status', 'approved')->count(),
            'rejected' => SemesterCourseRegistration::where('status', 'rejected')->count(),
        ];

        // Get top departments by the number of students
        $topDepartments = Department::withCount('students')
            ->orderBy('students_count', 'desc')
            ->take(5)
            ->get();

        // top department whose student has registered their courses
        $topDepartmentRegistered = Department::withCount(['courseEnrollments' => function ($query) use ($request) {
            // Apply the same filters as in the main query
            if ($request->filled('academic_session_id')) {
                $query->where('academic_session_id', $request->academic_session_id);
            }
            if ($request->filled('semester_id')) {
                $query->where('semester_id', $request->semester_id);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('course_enrollments.created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('course_enrollments.created_at', '<=', $request->end_date);
            }
        }])
            ->orderBy('course_enrollments_count', 'desc')
            ->take(5)
            ->get();
        return view('admin.all_course_registrations.index', compact('registrations', 'departments', 'academicSessions', 'semesters', 'stats', 'topDepartments', 'topDepartmentRegistered'));
    }

    public function show(SemesterCourseRegistration $registration)
    {
        $registration->load(['student', 'semester', 'academicSession', 'courseEnrollments.course', 'courseEnrollments']);
        return view('admin.all_course_registrations.show', compact('registration'));
    }

    public function export(Request $request)
    {
        $query = SemesterCourseRegistration::with(['student', 'semester', 'academicSession', 'student.department']);

        // Apply the same filters as in the index method
        if ($request->filled('department_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('student_id', 'like', "%{$search}%");
                })
                    ->orWhereHas('academicSession', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('semester', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }



        $registrations = $query->get();

        // Generate CSV
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['Student Name', 'Student ID', 'Department', 'Academic Session', 'Semester', 'Status', 'Total Credit Hours', 'Registration Date']);

        foreach ($registrations as $registration) {
            $csv->insertOne([
                $registration->student->user->full_name,
                $registration->student->matric_number,
                $registration->student->department->name,
                $registration->academicSession->name,
                $registration->semester->name,
                $registration->status,
                $registration->total_credit_hours,
                $registration->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="course_registrations.csv"',
        ];

        return response($csv->getContent(), 200, $headers);
    }


    public function approve(SemesterCourseRegistration $registration)
    {
        $registration->approve();
        return redirect()->back()->with('success', 'Course registration approved successfully.');
    }

    public function reject(SemesterCourseRegistration $registration)
    {
        $registration->reject();
        return redirect()->back()->with('success', 'Course registration rejected successfully.');
    }
}
