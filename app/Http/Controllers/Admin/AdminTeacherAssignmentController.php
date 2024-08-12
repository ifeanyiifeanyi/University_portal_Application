<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminTeacherAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $departments = Department::whereHas('courses.courseAssignments', function ($query) use ($currentSemester) {
            $query->where('semester_id', $currentSemester->id);
        })->with([
            'courses.courseAssignments' => function ($query) use ($currentSemester) {
                $query->where('semester_id', $currentSemester->id);
            },
            'courses.teachers',
            'teachers.courses'
        ])->get();

        $assignments = TeacherAssignment::with(['teacher.user', 'department', 'academicSession', 'semester', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.assignToLecturer.assign_course_department', compact(
            'departments',
            'currentAcademicSession',
            'currentSemester',
            'assignments'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(?Teacher $teacher = null)
    {

        $teachers = Teacher::all();

        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        // Filter departments that have courses in the current semester
        $departments = Department::whereHas('courses.courseAssignments', function ($query) use ($currentSemester) {
            $query->where('semester_id', $currentSemester->id);
        })->get();

        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();

        return view('admin.assignToLecturer.create', compact(
            'teacher',
            'teachers',
            'departments',
            'academicSessions',
            'semesters',
            'currentAcademicSession',
            'currentSemester'
        ));
    }

    // Controller method to filter courses in the assignment form for teachers
    public function getDepartmentCourses(Request $request)
    {
        $departmentId = $request->department_id;
        $semesterId = $request->semester_id;
        $assignmentId = $request->assignment_id;

        $cacheKey = "department_courses_{$departmentId}_{$semesterId}";

        $courses = Cache::remember($cacheKey, now()->addHours(1), function () use ($departmentId, $semesterId) {
            return Course::whereHas('courseAssignments', function ($query) use ($departmentId, $semesterId) {
                $query->where('department_id', $departmentId)
                    ->where('semester_id', $semesterId);
            })->with('courseAssignments')->get();
        });

        if ($assignmentId) {
            $assignedCourses = TeacherAssignment::where('id', $assignmentId)
                ->pluck('course_id')
                ->toArray();

            $courses->each(function ($course) use ($assignedCourses) {
                $course->is_assigned = in_array($course->id, $assignedCourses);
            });
        }

        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $validatedData = $request->validate([
            'teacher_id' => 'required|exists:teachers,user_id',
            'department_id' => 'required|exists:departments,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ], [
            'teacher_id.required' => 'Please select a lecturer for this assignment',
            'department_id' => 'The department is required',
            'course_ids' => 'Select at least a single course',
        ]);

        DB::beginTransaction();

        try {
            $teacher = Teacher::where('user_id', $validatedData['teacher_id'])->first();
            $assignments = [];

            foreach ($validatedData['course_ids'] as $courseId) {
                // Check if the course is already assigned to the teacher
                $existingAssignment = TeacherAssignment::where('teacher_id', $teacher->id)
                    ->where('course_id', $courseId)
                    ->where('department_id', $validatedData['department_id'])
                    ->where('academic_session_id', $validatedData['academic_session_id'])
                    ->where('semester_id', $validatedData['semester_id'])
                    ->first();

                if ($existingAssignment) {
                    $notification = [
                        'message' => 'This course has already been assigned to the selected lecturer.',
                        'alert-type' => 'error',
                    ];

                    return back()->with($notification);
                }

                $assignments[] = [
                    'teacher_id' => $teacher->id,
                    'department_id' => $validatedData['department_id'],
                    'academic_session_id' => $validatedData['academic_session_id'],
                    'semester_id' => $validatedData['semester_id'],
                    'course_id' => $courseId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Use batch insert
            TeacherAssignment::insert($assignments);

            DB::commit();
            $notification = [
                'message' => 'Department and courses has been successfully assigned to: ' . $teacher->first_name,
                'alert-type' => 'success',
            ];

            return redirect()->route('admin.teacher.assignment.view')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();
            $notification = [
                'message' => 'An error occurred while assigning courses. Please try again: ' . $e->getMessage(),
                'alert-type' => 'error',
            ];

            return back()->with($notification);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $assignment = TeacherAssignment::with(['teacher.user', 'department', 'academicSession', 'semester', 'course', 'courseAssignment'])
            ->findOrFail($id);

        return view('admin.assignToLecturer.show_assignment', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        // Filter departments that have courses in the current semester
        $departments = Department::whereHas('courses.courseAssignments', function ($query) use ($currentSemester) {
            $query->where('semester_id', $currentSemester->id);
        })->get();

        $assignment = TeacherAssignment::with(['teacher.user', 'department', 'academicSession', 'semester', 'course'])->findOrFail($id);

        $teachers = Teacher::all();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();

        return view('admin.assignToLecturer.edit', compact(
            'assignment',
            'teachers',
            'departments',
            'academicSessions',
            'semesters',
            'currentAcademicSession',
            'currentSemester'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'teacher_id' => 'required|exists:teachers,user_id',
            'department_id' => 'required|exists:departments,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ], [
            'teacher_id.required' => 'Please select a lecturer for this assignment',
            'department_id' => 'The department is required',
            'course_ids' => 'Select at least a single course',
        ]);

        DB::beginTransaction();

        try {
            $teacher = Teacher::where('user_id', $validatedData['teacher_id'])->first();

            $currentAssignments = TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('department_id', $validatedData['department_id'])
                ->where('academic_session_id', $validatedData['academic_session_id'])
                ->where('semester_id', $validatedData['semester_id'])
                ->pluck('course_id')
                ->toArray();

            $newAssignments = [];
            foreach ($validatedData['course_ids'] as $courseId) {
                if (!in_array($courseId, $currentAssignments)) {
                    // Check for duplicate assignments
                    $existingAssignment = TeacherAssignment::where('teacher_id', $teacher->id)
                        ->where('course_id', $courseId)
                        ->where('department_id', $validatedData['department_id'])
                        ->where('academic_session_id', $validatedData['academic_session_id'])
                        ->where('semester_id', $validatedData['semester_id'])
                        ->first();

                    if ($existingAssignment) {
                        $notification = [
                            'message' => 'This course has already been assigned to the selected lecturer.',
                            'alert-type' => 'error',
                        ];
                        return back()->with($notification);
                    }

                    $newAssignments[] = [
                        'teacher_id' => $teacher->id,
                        'department_id' => $validatedData['department_id'],
                        'academic_session_id' => $validatedData['academic_session_id'],
                        'semester_id' => $validatedData['semester_id'],
                        'course_id' => $courseId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Delete assignments that are no longer selected
            TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('department_id', $validatedData['department_id'])
                ->where('academic_session_id', $validatedData['academic_session_id'])
                ->where('semester_id', $validatedData['semester_id'])
                ->whereNotIn('course_id', $validatedData['course_ids'])
                ->delete();

            // Insert new assignments
            if (!empty($newAssignments)) {
                TeacherAssignment::insert($newAssignments);
            }

            DB::commit();
            $notification = [
                'message' => 'Department and courses has been successfully updated for: ' . $teacher->first_name,
                'alert-type' => 'success',
            ];
            return redirect()->route('admin.teacher.assignment.view')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();
            $notification = [
                'message' => 'An error occurred while updating courses: ' . $e->getMessage(),
                'alert-type' => 'error',
            ];

            return back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $assignment = TeacherAssignment::findOrFail($id);
            $assignment->delete();

            DB::commit();

            $notification = [
                'message' => 'Department and course assignment has been successfully removed for: ' . $assignment->teacher->user->first_name,
                'alert-type' => 'success',
            ];

            return response()->json(['success' => true, 'message' => $notification['message']]);
        } catch (\Exception $e) {
            DB::rollBack();
            $notification = [
                'alert-type' => 'error',
                'message' => 'An error occurred while removing the assignment: ' . $e->getMessage(),
            ];
            return response()->json(['success' => false, 'message' => $notification['message']]);
        }
    }
}
