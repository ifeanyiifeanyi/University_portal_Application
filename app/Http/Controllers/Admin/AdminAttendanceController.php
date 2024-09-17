<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdminAttendanceController extends Controller
{
    public function createAttendance()
    {
        $courses = Course::all();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $teachers = Teacher::all();

        $departments = Department::all();
        return view('admin.attendance.create', compact('courses', 'teachers', 'academicSessions', 'semesters', 'departments'));
    }

    // public function getStudentsByCourse($courseId)
    // {
    //     $course = Course::with(['students.user', 'teachers'])->findOrFail($courseId);
    //     $students = $course->students;
    //     $teacher = $course->teachers->first();

    //     return response()->json([
    //         'students' => $students,
    //         'teacher' => $teacher,
    //     ]);
    // }

    // public function getStudentsByCourse($courseId)
    // {
    //     $course = Course::with(['students.user'])->findOrFail($courseId);
    //     $students = $course->students;

    //     return response()->json([
    //         'students' => $students,
    //     ]);
    // }

    public function getStudentsByCourse(Request $request)
    {
        Log::info('Fetching students', $request->all());

        $courseId = $request->course_id;
        $departmentId = $request->department_id;
        $level = $request->level;

        $students = Student::whereHas('courses', function ($query) use ($courseId) {
            $query->where('courses.id', $courseId);
        })->where('department_id', $departmentId)
            ->where('level', $level)
            ->with('user')
            ->get();

        Log::info('Found students', ['count' => $students->count()]);

        return response()->json([
            'students' => $students,
        ]);
    }
}
