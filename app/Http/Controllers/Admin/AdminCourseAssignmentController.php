<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\CourseAssignment;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignCourseToDepartmentRequest;

// ** THIS CONTROLLER IS FOR ASSIGNING COURSES TO DEPARTMENTS, BASED ON ACADEMIC SESSION, SEMESTER, LEVEL AND DEPARTMENT **

class AdminCourseAssignmentController extends Controller
{
    public function index()
    {
        $assignments = CourseAssignment::with(['course', 'department', 'semester', 'academicSession'])->get();
        return view('admin.course_assignments.index', compact('assignments'));
    }




    public function create()
    {
        $courses = Course::all();
        $departments = Department::all();
        $semesters = Semester::all();
        $programs = Program::active()->get();
        $academicSessions = AcademicSession::all();

        return view('admin.course_assignments.create', compact('courses', 'departments', 'semesters', 'academicSessions'));
    }

    public function store(AssignCourseToDepartmentRequest $request)
    {
        $validated = $request->validated();

        try {
            // Fetch the department and course with their programs
            $departmentProgram = Department::with('program')->findOrFail($validated['department_id']);
            $courseProgram = Course::with('program')->findOrFail($validated['course_id']);

              // Validate program match
              if ($departmentProgram->program_id !== $courseProgram->program_id) {
                return back()->withErrors([
                    'course_id' => "The course's program ({$courseProgram->program->name}) does not match the department's program ({$departmentProgram->program->name})."
                ]);
            }


            $department = Department::findOrFail($validated['department_id']);
            $maxLevel = $department->duration * 100;

            if ($validated['level'] > $maxLevel) {
                return back()->withErrors(['level' => "The maximum level for this department is $maxLevel."]);
            }

            CourseAssignment::create($validated);

            return redirect()->route('course-assignments.index')->with('success', 'Course assigned successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if the error is a unique constraint violation
            if ($e->getCode() === '23000') {
                return back()->withErrors(['course_id' => 'This course is already assigned to the specified department, level, semester, and academic session.']);
            }

            throw $e; // Rethrow other exceptions
        }
    }


    public function show($semesterId, Request $request)
    {
        // Fetch the semester with related data
        $semester = Semester::with(['academicSession', 'courseAssignments.course', 'courseAssignments.department'])
            ->findOrFail($semesterId);

        // Group assignments first by department, then by level
        $groupedAssignments = $semester->courseAssignments
            ->groupBy('department_id')
            ->map(function ($departmentAssignments) {
                return $departmentAssignments->groupBy('level');
            });

        // Fetch all departments that have assignments
        $departments = Department::whereIn('id', $groupedAssignments->keys())->get();

        // Get filter parameters from the request
        $search = $request->input('search');
        $filterDepartment = $request->input('department');
        $filterLevel = $request->input('level');

        // Apply filters if any are set
        if ($search || $filterDepartment || $filterLevel) {
            $groupedAssignments = $groupedAssignments->map(function ($departmentAssignments, $departmentId) use ($search, $filterDepartment, $filterLevel) {
                // Filter by department if specified
                if ($filterDepartment && $filterDepartment != $departmentId) {
                    return collect();
                }
                return $departmentAssignments->map(function ($levelAssignments, $level) use ($search, $filterLevel) {
                    // Filter by level if specified
                    if ($filterLevel && $filterLevel != $level) {
                        return collect();
                    }
                    // Filter by search term if provided
                    return $levelAssignments->filter(function ($assignment) use ($search) {
                        return !$search || Str::contains(strtolower($assignment->course->code . ' ' . $assignment->course->title), strtolower($search));
                    });
                })->filter->isNotEmpty(); // Remove empty levels
            })->filter->isNotEmpty(); // Remove empty departments
        }

        // Get all unique levels for the filter dropdown
        $levels = $semester->courseAssignments->pluck('level')->unique()->sort()->values();

        // Return the view with all necessary data
        return view('admin.course_assignments.show', compact('semester', 'groupedAssignments', 'departments', 'levels', 'search', 'filterDepartment', 'filterLevel'));
    }

    public function destroy(CourseAssignment $courseAssignment)
    {
        // dd($courseAssignment);
        $courseAssignment->delete();

        $notification = [
            'message' => 'Course assignment deleted successfully!!',
            'success' => 'Course assignment deleted successfully!!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}
