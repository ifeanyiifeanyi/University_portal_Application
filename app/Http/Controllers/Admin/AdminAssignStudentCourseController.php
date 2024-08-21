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

        // Update the total credit hours in the semester registration
        $semesterRegistration->updateTotalCreditHours();

        return redirect()->route('admin.students.course-registrations', $student->id)->with('success', 'Courses registered successfully.');
    }

    // public function registerCourses(Request $request, $studentId)
    // {
    //     $request->validate([
    //         'courses' => 'required|array|min:1',
    //         'courses.*' => 'exists:courses,id',
    //     ]);

    //     $student = Student::findOrFail($studentId);
    //     $currentAcademicSession = AcademicSession::where('is_current', true)->firstOrFail();
    //     $currentSemester = Semester::where('is_current', true)->firstOrFail();

    //     $maxCreditHours = $student->department->semesters()
    //         ->where('semester_id', $currentSemester->id)
    //         ->firstOrFail()
    //         ->pivot
    //         ->max_credit_hours;

    //     // Retrieve or create the semester registration record
    //     $semesterRegistration = SemesterCourseRegistration::firstOrCreate(
    //         [
    //             'semester_id' => $currentSemester->id,
    //             'academic_session_id' => $currentAcademicSession->id,
    //             'student_id' => $student->id,
    //         ],
    //         ['status' => SemesterCourseRegistration::STATUS_PENDING]
    //     );

    //     // Retrieve the selected courses
    //     $selectedCourses = $request->input('courses', []);
    //     $courses = Course::whereIn('id', $selectedCourses)->get();

    //     // Recalculate the total credit hours for the selected courses
    //     $newTotalCreditHours = $semesterRegistration->courseEnrollments()
    //         ->whereNotIn('course_id', $selectedCourses)
    //         ->sum('courses.credit_hours') + $courses->sum('credit_hours');

    //     // Check if the new total credit hours exceed the maximum allowed
    //     if ($newTotalCreditHours > $maxCreditHours) {
    //         return redirect()->back()->with('error', 'Total credit hours exceed the maximum allowed for this semester.');
    //     }

    //     // Enroll or update the selected courses
    //     foreach ($courses as $course) {
    //         CourseEnrollment::updateOrCreate(
    //             [
    //                 'student_id' => $student->id,
    //                 'course_id' => $course->id,
    //                 'semester_course_registration_id' => $semesterRegistration->id,
    //             ],
    //             [
    //                 'department_id' => $student->department_id,
    //                 'level' => $student->current_level,
    //                 'status' => CourseEnrollment::STATUS_ENROLLED,
    //                 'academic_session_id' => $currentAcademicSession->id,
    //                 'registered_at' => now(),
    //             ]
    //         );
    //     }

    //     // Update the total credit hours in the semester registration
    //     $semesterRegistration->updateTotalCreditHours();

    //     return redirect()->route('admin.students.course-registrations', $student->id)
    //         ->with('success', 'Courses registered successfully.');
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




    public function removeCourse($studentId, $enrollmentId)
    {
        $enrollment = CourseEnrollment::findOrFail($enrollmentId);

        // Get the associated SemesterCourseRegistration
        $semesterCourseRegistration = $enrollment->semesterCourseRegistration;

        // Delete the enrollment
        $enrollment->delete();

        // Recalculate the total credit hours
        if ($semesterCourseRegistration) {
            $semesterCourseRegistration->updateTotalCreditHours();
        }

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
