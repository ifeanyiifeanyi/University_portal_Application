<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\TeacherAssignment;
use App\Http\Controllers\Controller;

class TeacherCoursesController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function courses(){
         // get the teachers details 
         $teacher = Teacher::with(['user'])->where('user_id',$this->authService->user()->id)->first();
         // get the department
         $coursesassigned = TeacherAssignment::with(['course','department','semester','academicSession'])->where('teacher_id',$teacher->id)->get();
         return view('teacher.courses.courses',[
            'coursesassigned'=>$coursesassigned
        ]);
    }
}
