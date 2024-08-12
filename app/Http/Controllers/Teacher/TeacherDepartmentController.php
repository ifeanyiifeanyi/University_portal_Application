<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Models\TeacherAssignment;

class TeacherDepartmentController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function departments(){
        // get the teachers details 
        $teacher = Teacher::with(['user'])->where('user_id',$this->authService->user()->id)->first();
        // get the department
        $departmentassigned = TeacherAssignment::with(['department'])->where('teacher_id',$teacher->id)->get();
        return view('teacher.departments.departments',[
            'departmentassigned'=>$departmentassigned
        ]);
    }
}
