<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CourseAssignment;
use App\Http\Controllers\Controller;

class AdminCourseAssignmentController extends Controller
{
    public function index()
    {
        $assignments = CourseAssignment::with(['course', 'department', 'semester'])->get();
        return view('admin.course_assignments.index', compact('assignments'));
    }

    public function create()
    {
        $courses = Course::all();
        $departments = Department::all();
        $semesters = Semester::all();

        return view('admin.course_assignments.create', compact('courses', 'departments', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'department_id' => 'required|exists:departments,id',
            'semester_id' => 'required|exists:semesters,id',
            'level' => 'required|integer|min:100|max:600|multiple_of:100',
        ]);

        $department = Department::findOrFail($request->department_id);
        $maxLevel = $department->duration * 100;

        if ($request->level > $maxLevel) {
            return back()->withErrors(['level' => "The maximum level for this department is $maxLevel."]);
        }

        
        CourseAssignment::create($request->all());



        return redirect()->route('course-assignments.index')->with('success', 'Course assigned successfully.');
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
