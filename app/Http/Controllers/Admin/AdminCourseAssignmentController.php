<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Semester;
use App\Models\Department;
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

    public function show($semesterId)
    {
        $semester = Semester::with(['academicSession', 'courseAssignments.course', 'courseAssignments.department'])
            ->findOrFail($semesterId);

        return view('admin.course_assignments.show', compact('semester'));
    }

    public function destroy(CourseAssignment $courseAssignment)
    {
        // dd($courseAssignment);
        $courseAssignment->delete();

        $notification = [
            'message' => 'Course assignment deleted successfully!!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

}
