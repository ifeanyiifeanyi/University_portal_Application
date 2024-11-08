<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()->latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function store(CourseRequest $request)
    {
        $course = Course::create($request->validated());
        $notification = [
            'message' => 'Course Created Successfully!!',
            'alert-type' => 'success'
        ];
        return response()->json([
            'notification' => $notification,
            'course' => $course
        ]);
    }

    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->validated());
        $notification = [
            'message' => 'Course Updated Successfully!!',
            'alert-type' => 'success'
        ];

        return response()->json([
            'notification' => $notification,
            'course' => $course
        ]);
    }

    public function destroy($id)
    {

        $user = Auth::user();
        //!! :TODO  check if course has active departments, teachers n students
        // if (!$user->isAdmin()) {
        //     $notification = [
        //         'message' => 'You are not authorized to delete this course!',
        //         'alert-type' => 'error'
        //     ];
        //     return redirect()->back()->with($notification);
        // }
        $course = Course::findOrFail($id);

        $course->delete();
        $notification = [
            'message' => 'Course Deleted Successfully!!',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }
}
