<?php

namespace App\Http\Controllers\Student;

use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;

class StudentFeesController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function index(){
        return view('student.fees.index');
    }
    public function view(){
        return view('student.fees.view');
    }

    public function pay(){
         // load the semester
         $semesters = Semester::get();
         // load the academic sessions
         $academicsessions = AcademicSession::all();
         // load the studentprofile
         $student = Student::where('user_id',$this->authService->user()->id)->first();
         
       return view('student.fees.pay',[
           'semesters'=>$semesters,
           'academicsessions'=>$academicsessions,
           'student'=>$student
       ]);
    }
}
