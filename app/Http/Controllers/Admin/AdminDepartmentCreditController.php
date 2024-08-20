<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'semester_id' => 'required|exists:semesters,id',
            'max_credit_hours' => 'required|numeric|min:1',
            'level' => 'required|multiple_of:100'
        ], [
            'department_id.required' => 'Department is required',
            'department_id.exists' => 'Department does not exist',
            'semester_id.required' => 'Semester is required',
            'level.required' => 'Department Level is required'
        ]);

        $department = Department::findOrFail($request->department_id);
        $maxLevel = $department->duration * 100;

        if ($request->level > $maxLevel) {
            return back()->with([
                'message' =>  'Level must be less than or equal to ' . $maxLevel,
                'alert-type' => 'error'
            ]);
        }

        $existingAssignment = DB::table('department_semester')
            ->where('department_id', $request->department_id)  // AND condition
            ->where('level', $request->level)  // AND condition
            ->first();


        if ($existingAssignment) {
            return back()->with([
                'message' => 'Credit load for this level already exists.',
                'alert-type' => 'error'
            ]);
        }

        // Create a new assignment
        DB::table('department_semester')->insert([
            'department_id' => $request->department_id,
            'semester_id' => $request->semester_id,
            'level' => $request->level,
            'max_credit_hours' => $request->max_credit_hours,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.department.credit.view')->with([
            'message' => 'Credit load assigned successfully',
            'alert-type' => 'success'
        ]);
    }

    public function edit($id)
    {
        // $creditAssignment = DB::table('department_semester')
        //     ->join('departments', 'department_semester.department_id', '=', 'departments.id')
        //     ->join('semesters', 'department_semester.semester_id', '=', 'semesters.id')
        //     ->select('department_semester.*', 'departments.name as department_name', 'semesters.name as semester_name')
        //     ->where('department_semester.id', $id)
        //     ->first();
        $creditAssignment = DB::table('department_semester')->where('id', $id)->first();


        $departments = Department::all();
        $semesters = Semester::all();

        return view('admin.department_credits.edit', compact('creditAssignment', 'departments', 'semesters'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'max_credit_hours' => 'required|integer|min:1'
        ]);

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
