<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\CourseAssignment;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SemesterCourseRegistration;

class AdminAssignStudentCourseController extends Controller
{
    public function index($student_id = null) {}

    // public function showSemesterCourses($studentId)
    // {
    //     $student = Student::findOrFail($studentId);
    //     $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
    //     $currentSemester = Semester::where('is_current', true)->firstOrFail();

    //     // Fetch the max credit hours from the pivot table
    //     $maxCreditHours = $student->department->semesters()
    //         ->where('semester_id', $currentSemester->id)
    //         ->firstOrFail()
    //         ->pivot
    //         ->max_credit_hours;

    //     // Fetch Course Assignments for the student's department and current semester
    //     $courseAssignments = CourseAssignment::where('department_id', $student->department_id)
    //         ->where('semester_id', $currentSemester->id)
    //         ->with('course')
    //         ->get();

    //     // Fetch the student's semester registration
    //     $semesterRegistration = SemesterCourseRegistration::where([
    //         'student_id' => $student->id,
    //         'academic_session_id' => $currentAcademicSession->id,
    //         'semester_id' => $currentSemester->id,
    //     ])->first();

    //     // If the registration is approved, fetch the enrolled courses
    //     if ($semesterRegistration && $semesterRegistration->status == 'approved') {
    //         $enrolledCourses = CourseEnrollment::where('student_id', $student->id)
    //             ->where('semester_course_registration_id', $semesterRegistration->id)
    //             ->whereIn('course_id', $courseAssignments->pluck('course_id')) // Ensure courses are assigned
    //             ->pluck('course_id')
    //             ->toArray();
    //     } else {
    //         $enrolledCourses = []; // Or handle differently if needed
    //     }

    //     return view('admin.course_registrations.index', compact(
    //         'courseAssignments',
    //         'enrolledCourses',
    //         'student',
    //         'maxCreditHours',
    //         'currentSemester',
    //         'currentAcademicSession'
    //     ));
    // }


    // public function showSemesterCourses($studentId)
    // {
    //     $student = Student::findOrFail($studentId);
    //     $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
    //     $currentSemester = Semester::where('is_current', true)->firstOrFail();


    //     // Fetch the max credit hours from the pivot table
    //     // $maxCreditHours = $student->department->semesters()
    //     //     ->where('semester_id', $currentSemester->id)
    //     //     ->firstOrFail()
    //     //     ->pivot
    //     //     ->max_credit_hours;
    //     // dd($maxCreditHours);
    //         $maxCreditHours = DB::table('department_semester')
    //     ->where('department_id', $student->department->id)
    //     ->where('semester_id', $currentSemester->id)
    //     ->where('level', $student->current_level)
    //     ->value('max_credit_hours');



    //     $courseAssignments = CourseAssignment::where('department_id', $student->department_id)
    //         ->where('semester_id', $currentSemester->id)
    //         ->with('course')
    //         ->get();

    //     $enrolledCourses = CourseEnrollment::where('student_id', $student->id)
    //         ->where('semester_course_registration_id', function ($query) use ($currentSemester, $currentAcademicSession, $student) {
    //             $query->select('id')
    //                 ->from('semester_course_registrations')
    //                 ->where('semester_id', $currentSemester->id)
    //                 ->where('academic_session_id', $currentAcademicSession->id)
    //                 ->where('student_id', $student->id);
    //         })
    //         ->pluck('course_id')
    //         ->toArray();
    //     $totalCreditHours = Course::whereIn('id', $enrolledCourses)->sum('credit_hours');

    //     return view('admin.course_registrations.index', compact(
    //         'courseAssignments',
    //         'enrolledCourses',
    //         'student',
    //         'maxCreditHours',
    //         'currentSemester',
    //         'currentAcademicSession',
    //         'totalCreditHours'
    //     ));

    //     $semesterRegistration = SemesterCourseRegistration::where([
    //         'student_id' => $student->id,
    //         'semester_id' => $currentSemester->id,
    //         'academic_session_id' => $currentAcademicSession->id,
    //     ])->first();

    //     if ($semesterRegistration && $semesterRegistration->status == 'approved') {
    //         $enrolledCourses = CourseEnrollment::where('student_id', $student->id)
    //             ->where('semester_course_registration_id', $semesterRegistration->id)
    //             ->pluck('course_id')
    //             ->toArray();
    //     } else {
    //         // If registration is not approved, you might want to return an empty array
    //         // or display a message saying the registration is pending approval.
    //         $enrolledCourses = [];
    //     }
    // }


    public function showSemesterCourses($studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
        $currentSemester = Semester::where('is_current', true)->firstOrFail();

        // Fetch the max credit hours from the department_semester pivot table
        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $currentSemester->id)
            ->where('level', $student->current_level)
            ->firstOrFail()
            ->pivot
            ->max_credit_hours;

        // Fetch all course assignments for the student's department and current semester
        $courseAssignments = CourseAssignment::where('department_id', $student->department_id)
            ->where('semester_id', $currentSemester->id)
            ->with('course')
            ->get();

        // Check if there's an existing semester registration
        $semesterRegistration = SemesterCourseRegistration::where([
            'student_id' => $student->id,
            'semester_id' => $currentSemester->id,
            'academic_session_id' => $currentAcademicSession->id,
        ])->first();

        $enrolledCourses = [];
        $totalCreditHours = 0;

        if ($semesterRegistration) {
            $enrolledCourses = CourseEnrollment::where('semester_course_registration_id', $semesterRegistration->id)
                ->pluck('course_id')
                ->toArray();

            $totalCreditHours = Course::whereIn('id', $enrolledCourses)->sum('credit_hours');
        }

        return view('admin.course_registrations.index', compact(
            'courseAssignments',
            'enrolledCourses',
            'student',
            'maxCreditHours',
            'currentSemester',
            'currentAcademicSession',
            'totalCreditHours'
        ));
    }



    public function registerCourses(Request $request, $studentId)
    {
        $request->validate([
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
        ]);

        $student = Student::findOrFail($studentId);
        $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
        $currentSemester = Semester::where('is_current', true)->firstOrFail();

        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $currentSemester->id)
            ->firstOrFail()
            ->pivot
            ->max_credit_hours;
        // dd($maxCreditHours);

        $semesterRegistration = SemesterCourseRegistration::firstOrCreate(
            [
                'semester_id' => $currentSemester->id,
                'academic_session_id' => $currentAcademicSession->id,
                'student_id' => $student->id,
            ],
            ['status' => SemesterCourseRegistration::STATUS_PENDING]
        );

        $selectedCourses = $request->input('courses', []);
        $courses = Course::whereIn('id', $selectedCourses)->get();

        $totalCreditHours = $courses->sum('credit_hours');

        if ($totalCreditHours > $maxCreditHours) {
            return redirect()->back()->with('error', 'Total credit hours exceed the maximum allowed for this semester.');
        }

        foreach ($courses as $course) {
            CourseEnrollment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'semester_course_registration_id' => $semesterRegistration->id,
                ],
                [
                    'department_id' => $student->department_id,
                    'level' => $student->current_level,
                    'status' => CourseEnrollment::STATUS_ENROLLED,
                    'academic_session_id' => $currentAcademicSession->id,
                    'registered_at' => now(),
                ]
            );
        }

        $semesterRegistration->total_credit_hours = $totalCreditHours;
        $semesterRegistration->save();

        return redirect()->route('admin.students.course-registrations', $student->id)->with('success', 'Courses registered successfully.');
    }



    // SHOW COURSES REGISTERED TO THE STUDENT
    // public function showStudentCourseRegistrations($studentId)
    // {
    //     $student = Student::findOrFail($studentId);
    //     $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
    //     $currentSemester = Semester::where('is_current', true)->firstOrFail();

    //     // Fetch the max credit hours from the department_semester pivot table
    //     $maxCreditHours = $student->department->semesters()
    //         ->where('semester_id', $currentSemester->id)
    //         ->where('level', $student->current_level)
    //         ->firstOrFail()
    //         ->pivot
    //         ->max_credit_hours;

    //     // dd($maxCreditHours);


    //     $semesterRegistration = SemesterCourseRegistration::where([
    //         'student_id' => $student->id,
    //         'academic_session_id' => $currentAcademicSession->id,
    //         'semester_id' => $currentSemester->id,
    //     ])->firstOrFail();

    //     $enrolledCourses = CourseEnrollment::where('semester_course_registration_id', $semesterRegistration->id)
    //         ->with('course')
    //         ->get();

    //     $totalCreditHours = $enrolledCourses->sum('course.credit_hours');
    //     return view('admin.student.course_registrations', compact('student', 'semesterRegistration', 'enrolledCourses', 'totalCreditHours', 'currentAcademicSession', 'currentSemester', 'maxCreditHours'));
    // }

    public function showStudentCourseRegistrations($studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
        $currentSemester = Semester::where('is_current', true)->firstOrFail();

        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $currentSemester->id)
            ->where('level', $student->current_level)
            ->firstOrFail()
            ->pivot
            ->max_credit_hours;

        $semesterRegistration = SemesterCourseRegistration::where([
            'student_id' => $student->id,
            'academic_session_id' => $currentAcademicSession->id,
            'semester_id' => $currentSemester->id,
        ])->firstOrFail();

        $enrolledCourses = CourseEnrollment::where('semester_course_registration_id', $semesterRegistration->id)
            ->with('course', 'semesterCourseRegistration', 'department')
            ->get();

        $totalCreditHours = $enrolledCourses->sum('course.credit_hours');

        return view('admin.student.course_registrations', compact(
            'student',
            'semesterRegistration',
            'enrolledCourses',
            'totalCreditHours',
            'currentAcademicSession',
            'currentSemester',
            'maxCreditHours'
        ));
    }


    // THIS IS FOR ANY WRONG REGISTRATION
    public function removeCourse($studentId, $enrollmentId)
    {
        $enrollment = CourseEnrollment::findOrFail($enrollmentId);
        $enrollment->delete();

        return redirect()->back()->with('success', 'Course removed successfully.');
    }

    // HERE WE ARE SUPPOSE TO APPROVE THE REGISTERED STUDENT COURSES
    public function approveRegistration(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
        $currentSemester = Semester::where('is_current', true)->firstOrFail();

        $semesterRegistration = SemesterCourseRegistration::where([
            'student_id' => $student->id,
            'academic_session_id' => $currentAcademicSession->id,
            'semester_id' => $currentSemester->id,
        ])->firstOrFail();

        $semesterRegistration->status = $request->input('status');
        $semesterRegistration->save();

        return redirect()->back()->with([
            'alert-type' => 'success',
            'message' => 'Registration status updated successfully.'
        ]);
    }


    public function updateCourseStatus(Request $request, $studentId, $enrollmentId)
    {
        $student = Student::findOrFail($studentId);
        $enrollment = $student->enrollments()->where('id', $enrollmentId)->first();
        $enrollment->status = $request->status;
        $enrollment->save();

        return redirect()->back()->with([
            'alert-type' => 'success',
            'message' => 'Course status updated successfully.'
        ]);
    }
}
