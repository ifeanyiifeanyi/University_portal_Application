<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Symfony\Component\ErrorHandler\Debug;

class DepartmentController extends Controller
{
    public function index()
    {
        $faculties = Faculty::query()->latest()->get();
        $departments = Department::query()->latest()->get();
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

    public function show($id){
        $department = Department::findOrFail($id);
        return view('admin.departments.detail', compact('department'));
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
}
