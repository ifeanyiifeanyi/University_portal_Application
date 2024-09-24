<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentprofileRequest;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
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
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        $student = Student::where('user_id',$this->authService->user()->id)->first();

        return view('student.dashboard',[
            'student'=>$student
        ]);
    }

    public function profile(){
        $getuser = User::where('id',$this->authService->user()->id)->first();
        $profile = Student::where('user_id',$this->authService->user()->id)->first();
        return view('student.profile.profile',[
            'student'=>$profile,
            'getuser'=>$getuser
        ]);
    }

    public function createprofile(StudentprofileRequest $createstudentprofile){

    }
    public function updateprofile(StudentprofileRequest $updatestudentprofile, StudentService $studentservice){
        return $studentservice->updateprofile($updatestudentprofile);
    }

    public function virtualid(){
        $getuser = User::where('id',$this->authService->user()->id)->first();
        $profile = Student::with('department')->where('user_id',$this->authService->user()->id)->first();
        return view('student.profile.virtualid',[
            'student'=>$profile,
            'getuser'=>$getuser
        ]);
    }

    
}
