<?php

namespace App\Http\Controllers\Teacher;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\TeacherService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Profilerequest;
use App\Models\Teacher;

class TeacherController extends Controller
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

        return view('teacher.dashboard');
    }

    public function profile(){
        $getuser = User::where('id',$this->authService->user()->id)->first();
        $profile = Teacher::where('user_id',$this->authService->user()->id)->first();
        return view('teacher.profile.profile',[
            'profile'=>$profile,
            'getuser'=>$getuser
        ]);
    }

    public function createprofile(Profilerequest $createprofile, TeacherService $teacherservice){
        return $teacherservice->createprofile($createprofile);
    }
    public function updateprofile(Profilerequest $updateprofile, TeacherService $teacherservice){
        return $teacherservice->updateprofile($updateprofile);
    }
}
