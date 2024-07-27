<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\CourseAssignment;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use Symfony\Component\ErrorHandler\Debug;

class DepartmentController extends Controller
{
    public function index()
    {
        $faculties = Faculty::query()->latest()->get();
        $departments = Department::query()->oldest()->get();
        return view('admin.departments.index', compact('faculties', 'departments'));
    }


    public function store(DepartmentRequest $request)
    {
        $validatedData = $request->validated();

        // Available alpha characters
        $characters = 'SHNDP';

        // generate a pin based on 2 * 7 digits + a random character
        $validatedData['code'] = mt_rand(1000000, 9999999)
            . mt_rand(1000000, 9999999)
            . $characters;

        Department::create($validatedData);
        $notification = [
            'message' => 'New Department Created Successfully!!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    public function edit($id)
    {
        $departmentSingle = Department::findOrFail($id);
        $faculties = Faculty::query()->latest()->get();
        $departments = Department::query()->latest()->get();
        return view('admin.departments.index', compact('departmentSingle', 'faculties', 'departments'));
    }
    public function update(DepartmentRequest $request, $id)
    {
        // dump($id);

        $department = Department::findOrFail($id);
        $department->update($request->validated());
        $notification = [
            'message' => 'Department Updated Successfully!!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.department.view')->with($notification);
    }

    public function show($id, Request $request)
    {
        $department = Department::findOrFail($id);
        $query = CourseAssignment::with(['course', 'semester.academicSession', 'teacherAssignment.teacher.user'])->where('department_id', $id);



        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('teacherAssignments.teacher.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('session')) {
            $query->whereHas('semester.academicSession', function ($q) use ($request) {
                $q->where('name', $request->input('session'));
            });
        }

        if ($request->has('semester')) {
            $query->whereHas('semester', function ($q) use ($request) {
                $q->where('name', $request->input('semester'));
            });
        }

        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        $assignments = $query->orderBy('semester_id', 'desc')->paginate(15);


        return view('admin.departments.detail', compact('department', 'assignments'));
    }



    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        $department->delete();
        $notification = [
            'message' => 'Department Deleted Successfully!!',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    // fetch the department academic levels
    public function levels(Department $department)
    {
        return response()->json($department->levels);
    }
}
