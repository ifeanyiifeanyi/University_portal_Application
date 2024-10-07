<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminDepartmentCreditForSemester;
use App\Http\Requests\AdminDepartmentCreditForSemesterUpdate;

class AdminDepartmentCreditController extends Controller
{
    public function levels(Department $department)
    {
        return response()->json($department->levels);
    }


    public function index()
    {

        $departments = Department::all();
        $semesters = Semester::all();
        $creditAssignments = DB::table('department_semester')
            ->join('departments', 'department_semester.department_id', '=', 'departments.id')
            ->join('semesters', 'department_semester.semester_id', '=', 'semesters.id')
            ->select('department_semester.*', 'departments.name as department_name', 'semesters.name as semester_name')
            ->orderByDesc('department_id')
            ->get();

        return view('admin.department_credits.index', compact('departments', 'semesters', 'creditAssignments'));
    }

    public function create()
    {
        $departments = Department::all();
        $semesters = Semester::all();
        return view('admin.department_credits.create', compact('departments', 'semesters'));
    }

    public function store(AdminDepartmentCreditForSemester $request)
    {
        DB::table('department_semester')->insert($request->validated());

        return redirect()->route('admin.department.credit.view')->with([
            'message' => 'Credit load assigned successfully',
            'alert-type' => 'success'
        ]);
    }


    public function edit($id)
    {

        $creditAssignment = DB::table('department_semester')->where('id', $id)->first();


        $departments = Department::all();
        $semesters = Semester::all();

        return view('admin.department_credits.edit', compact('creditAssignment', 'departments', 'semesters'));
    }


    public function update(AdminDepartmentCreditForSemesterUpdate $request, $id)
    {
        DB::table('department_semester')
            ->where('id', $id)
            ->update(['max_credit_hours' => $request->max_credit_hours]);

        return redirect()->route('admin.department.credit.view')->with([
            'message' => 'Credit load Updated successfully',
            'alert-type' => 'success'
        ]);
    }

    public function destroy($id)
    {
        DB::table('department_semester')->where('id', $id)->delete();

        return redirect()->route('admin.department.credit.view')->with([
            'message' => 'Credit load deleted successfully',
            'alert-type' => 'success'
        ]);
    }
}
