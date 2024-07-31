<?php

namespace App\Services;

use App\Http\Requests\Profilerequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class TeacherService
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }

   
    public function createprofile(Profilerequest $createprofile){
    //   generate the employee id
    $employeeid = $this->generateemployeeid();

        $getuser = User::where('id',$this->authService->user()->id)->first();
        // check if teacher already exists
        $checkteacher = Teacher::where('user_id',$this->authService->user()->id)->first();
        if(!$checkteacher){
            $createteacher = $getuser->teacher()->create([
                'date_of_birth'=>$createprofile->date_of_birth,
                'gender'=>$createprofile->gender,
                'teaching_experience'=>$createprofile->teaching_experience,
                'teacher_type'=>$createprofile->teacher_type,
                'teacher_qualification'=>$createprofile->teacher_qualification,
                'teacher_title'=>$createprofile->teacher_title,
                'employment_id'=>$employeeid,
                'date_of_employment'=>$createprofile->date_of_employment,
                'address'=>$createprofile->address,
                'nationality'=>$createprofile->nationality,
                'level'=>$createprofile->teacher_level
            ]);
            if($createteacher){
                return redirect(route('teacher.view.profile'))->with('success','Profile created successfully');
            }
        }

        
    }
    public function updateprofile(Profilerequest $updateprofile){
        
       
            $updateteacher = Teacher::where('user_id',$updateprofile->user_id)->update([
                'date_of_birth'=>$updateprofile->date_of_birth,
                'gender'=>$updateprofile->gender,
                'teaching_experience'=>$updateprofile->teaching_experience,
                'teacher_type'=>$updateprofile->teacher_type,
                'teacher_qualification'=>$updateprofile->teacher_qualification,
                'teacher_title'=>$updateprofile->teacher_title,
                'date_of_employment'=>$updateprofile->date_of_employment,
                'address'=>$updateprofile->address,
                'nationality'=>$updateprofile->nationality,
                'level'=>$updateprofile->teacher_level,
                'office_hours'=>$updateprofile->office_hours,
                'office_address'=>$updateprofile->office_address,
                'certifications'=>$updateprofile->certifications,
                'publications'=>$updateprofile->publications
            ]);
            if($updateteacher){
                return redirect(route('teacher.view.profile'))->with('success','Profile updated successfully');
            }
        
    }

    public function generateemployeeid(){
        // check to know if the id already exists for the user
        do{
            $employeeid = bin2hex(random_bytes(8));
        }while(Teacher::where('employment_id',$employeeid)->exists());
        return $employeeid;
    }
}