<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\CourseAssignment;
use App\Models\CourseEnrollment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\SemesterCourseRegistration;

class AdminAssignStudentCourseController extends Controller
{
    public function index($student_id = null)
    {
    }


    public function showSemesterCourses($studentId)
    {
        // Fetch the student
        $student = Student::findOrFail($studentId);

        // Fetch the current academic session and semester
        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        // Ensure both the academic session and semester are set
        if (!$currentAcademicSession || !$currentSemester) {
            return redirect()->back()->with('error', 'Current academic session or semester not set.');
        }

        // Fetch the course assignments for the student's department and current semester
        $courseAssignments = CourseAssignment::where('department_id', $student->department_id)
            ->where('semester_id', $currentSemester->id)
            ->with('course')
            ->get();

        // Check for already enrolled courses
        $enrolledCourses = CourseEnrollment::where('student_id', $student->id)
            ->where('semester_course_registration_id', function ($query) use ($currentSemester, $currentAcademicSession, $student) {
                $query->select('id')
                    ->from('semester_course_registrations')
                    ->where('semester_id', $currentSemester->id)
                    ->where('academic_session_id', $currentAcademicSession->id)
                    ->where('student_id', $student->id);
            })
            ->pluck('course_id')
            ->toArray();

        // Fetch carryover courses (courses not passed previously)
        $carryoverAssignments = CourseEnrollment::where('student_id', $student->id)
            ->whereNull('grade')
            ->whereNotIn('course_id', $enrolledCourses)
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->values();

        // Fetch the maximum credit hours from the pivot table
        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $currentSemester->id)
            ->first()
            ->pivot
            ->max_credit_hours ?? null;

        // $maxCreditHours = $pivotRecord ? $pivotRecord->pivot->max_credit_hours : null;



        return view('admin.course_registrations.index', compact(
            'courseAssignments',
            'enrolledCourses',
            'carryoverAssignments',
            'student',
            'maxCreditHours',
            'currentSemester',
            'currentAcademicSession'
        ));
    }


    public function registerCourses(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $department = $student->department;

        // Fetch the current academic session and semester
        $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
        $currentSemester = Semester::where('is_current', true)->firstOrFail();

        // Fetch the maximum credit hours from the pivot table
        $maxCreditHours = $department->semesters()
            ->where('semester_id', $currentSemester->id)
            ->first()
            ->pivot
            ->max_credit_hours;

        // Ensure the semester course registration record exists
        $semesterRegistration = SemesterCourseRegistration::firstOrCreate(
            [
                'semester_id' => $currentSemester->id,
                'academic_session_id' => $currentAcademicSession->id,
                'student_id' => $student->id,
            ],
            [
                'status' => SemesterCourseRegistration::STATUS_PENDING,
            ]
        );

        // Get selected regular courses and carryover courses
        $selectedCourses = Course::whereIn('id', $request->courses)->get();
        $carryoverCourses = Course::whereIn('id', $request->carryover_courses ?? [])->get();

        // Calculate credit hours
        $regularCreditHours = $selectedCourses->sum('credit_hours');
        $carryoverCreditHours = $carryoverCourses->sum('credit_hours');
        $totalCreditHours = $regularCreditHours + $carryoverCreditHours;

        // Check if total credit hours exceed the maximum allowed
        if ($totalCreditHours > $maxCreditHours) {
            return redirect()->back()->with('error', 'Total credit hours exceed the maximum allowed for this semester.');
        }

        // Function to create or update course enrollment
        $createOrUpdateEnrollment = function ($course, $isCarryover) use ($student, $semesterRegistration, $currentAcademicSession) {
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
                    'is_carryover' => $isCarryover,
                ]
            );
        };

        // Register regular courses
        foreach ($selectedCourses as $course) {
            $createOrUpdateEnrollment($course, false);
        }

        // Register carryover courses
        foreach ($carryoverCourses as $course) {
            $createOrUpdateEnrollment($course, true);
        }

        // Update the total credit hours in the SemesterCourseRegistration
        $semesterRegistration->total_credit_hours = $totalCreditHours;
        $semesterRegistration->save();

        return redirect()->route('admin.student.view', $student->id)
            ->with([
                'message' => 'Courses registered successfully.',
                'alert-type' => 'success'
            ]);
    }



    public function showStudentCourseRegistrations($studentId)
    {
        $student = Student::findOrFail($studentId);

        $registrations = SemesterCourseRegistration::with(['semester', 'academicSession', 'courseEnrollments.course'])
            ->where('student_id', $studentId)
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get()
            ->groupBy(['academic_session_id', 'semester_id']);

        return view('admin.student.course_registrations', compact('student', 'registrations'));
    }
}
