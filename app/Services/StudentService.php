<?php

namespace App\Services;

use App\Http\Requests\Profilerequest;
use App\Http\Requests\StudentprofileRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class StudentService
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }

    public function createprofile(StudentprofileRequest $updatestudentprofileprofile){

    }
    public function updateprofile(StudentprofileRequest $updatestudentprofile){
        $updatestudent = Student::where('user_id',$this->authService->user()->id)->update([
            'date_of_birth' => $updatestudentprofile['date_of_birth'],
            'gender' => $updatestudentprofile['gender'],
            'state_of_origin' => $updatestudentprofile['state_of_origin'],
            'lga_of_origin' => $updatestudentprofile['local_govt_of_origin'],
            'hometown' => $updatestudentprofile['hometown'],
            'residential_address' => $updatestudentprofile['residential_address'],
            'permanent_address' => $updatestudentprofile['permanent_address'],
            'nationality' => $updatestudentprofile['nationality'],
            'marital_status' => $updatestudentprofile['marital_status'],
            'religion' => $updatestudentprofile['religion'],
            'blood_group' => $updatestudentprofile['bloodgroup'],
            'genotype' => $updatestudentprofile['genotype'],
            'next_of_kin_name' => $updatestudentprofile['next_of_kin_name'],
            'next_of_kin_relationship' => $updatestudentprofile['next_of_kin_relationship'],
            'next_of_kin_phone' => $updatestudentprofile['next_of_kin_phone'],
            'next_of_kin_address' => $updatestudentprofile['next_of_kin_address'],
            'jamb_registration_number' => $updatestudentprofile['jamb_registration_number'],
            'year_of_admission' => $updatestudentprofile['year_of_admission'],
            'mode_of_entry' => $updatestudentprofile['mode_of_entry'],
            'current_level' => $updatestudentprofile['current_level']
        ]);
        // update for user tables

        $updateuser = User::where('id',$this->authService->user()->id)->update([
            'first_name'=>$updatestudentprofile->firstname,
            'last_name'=>$updatestudentprofile->lastname,
            'other_name'=>$updatestudentprofile->othernames,
            'phone'=>$updatestudentprofile->phonenumber,
        ]);

        if($updatestudent && $updateuser){
            return redirect(route('student.view.profile'));
         }
    }
}