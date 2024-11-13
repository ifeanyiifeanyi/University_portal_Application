<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Services\AuthService;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Profilerequest;
use App\Services\StudentBatchService;
use App\Http\Requests\StudentprofileRequest;
use Illuminate\Contracts\Auth\StatefulGuard;

class StudentService
{
    protected $authService;
    protected $studentBatchService;


    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService, StudentBatchService $studentBatchService)
    {

        $this->authService = $authService;
        $this->studentBatchService = $studentBatchService;
    }

    public function createprofile(StudentprofileRequest $updatestudentprofileprofile) {}
    // public function updateprofile(StudentprofileRequest $updatestudentprofile){
    //     $updatestudent = Student::where('user_id',$this->authService->user()->id)->update([
    //         'date_of_birth' => $updatestudentprofile['date_of_birth'],
    //         'gender' => $updatestudentprofile['gender'],
    //         'state_of_origin' => $updatestudentprofile['state_of_origin'],
    //         'lga_of_origin' => $updatestudentprofile['local_govt_of_origin'],
    //         'hometown' => $updatestudentprofile['hometown'],
    //         'residential_address' => $updatestudentprofile['residential_address'],
    //         'permanent_address' => $updatestudentprofile['permanent_address'],
    //         'nationality' => $updatestudentprofile['nationality'],
    //         'marital_status' => $updatestudentprofile['marital_status'],
    //         'religion' => $updatestudentprofile['religion'],
    //         'blood_group' => $updatestudentprofile['bloodgroup'],
    //         'genotype' => $updatestudentprofile['genotype'],
    //         'next_of_kin_name' => $updatestudentprofile['next_of_kin_name'],
    //         'next_of_kin_relationship' => $updatestudentprofile['next_of_kin_relationship'],
    //         'next_of_kin_phone' => $updatestudentprofile['next_of_kin_phone'],
    //         'next_of_kin_address' => $updatestudentprofile['next_of_kin_address'],
    //         'jamb_registration_number' => $updatestudentprofile['jamb_registration_number'],
    //         'year_of_admission' => $updatestudentprofile['year_of_admission'],
    //         'mode_of_entry' => $updatestudentprofile['mode_of_entry'],
    //         'current_level' => $updatestudentprofile['current_level']
    //     ]);
    //     // update for user tables

    //     $updateuser = User::where('id',$this->authService->user()->id)->update([
    //         'first_name'=>$updatestudentprofile->firstname,
    //         'last_name'=>$updatestudentprofile->lastname,
    //         'other_name'=>$updatestudentprofile->othernames,
    //         'phone'=>$updatestudentprofile->phonenumber,
    //     ]);

    //     if($updatestudent && $updateuser){
    //         return redirect(route('student.view.profile'));
    //      }
    // }


    public function createStudent(array $data)
    {
        DB::beginTransaction();

        try {
            $matNumber = $this->studentBatchService->generateMatricNumber();

            // Create user
            $user = User::create([
                'user_type' => User::TYPE_STUDENT,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'other_name' => $data['other_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'username' => mt_rand(1000, 9999),
                'slug' => 'SLUG' . mt_rand(1000, 9999),
                'email_verified_at' => now(),
                'password' => Hash::make($data['password']) ?? Hash::make('password'),
            ]);

            // Handle profile photo upload
            if (isset($data['profile_photo']) && $data['profile_photo']) {
                $profilePhoto = $data['profile_photo'];
                $extension = $profilePhoto->getClientOriginalExtension();
                $profilePhotoName = time() . "." . $extension;
                $profilePhoto->move('admin/students/profile/', $profilePhotoName);
                $user->profile_photo = 'admin/students/profile/' . $profilePhotoName;
                $user->save();
            }

            // Create student
            $student = Student::create([
                'user_id' => $user->id,
                'department_id' => $data['department_id'],
                'matric_number' => $matNumber,
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'state_of_origin' => $data['state_of_origin'],
                'lga_of_origin' => $data['lga_of_origin'],
                'hometown' => $data['hometown'],
                'residential_address' => $data['residential_address'],
                'permanent_address' => $data['permanent_address'],
                'nationality' => $data['nationality'],
                'marital_status' => $data['marital_status'],
                'religion' => $data['religion'],
                'blood_group' => $data['blood_group'],
                'genotype' => $data['genotype'],
                'next_of_kin_name' => $data['next_of_kin_name'],
                'next_of_kin_relationship' => $data['next_of_kin_relationship'],
                'next_of_kin_phone' => $data['next_of_kin_phone'],
                'next_of_kin_address' => $data['next_of_kin_address'],
                'jamb_registration_number' => $data['jamb_registration_number'] ?? null,
                'year_of_admission' => $data['year_of_admission'],
                'mode_of_entry' => $data['mode_of_entry'],
                'current_level' => $data['current_level'],
            ]);



            DB::commit();

            // Log the activity
            $this->logActivity($user, $student);

            // Dispatch the welcome email job
            SendWelcomeEmail::dispatch($user, $student)->delay(now()->addMinutes(5));

            return $student;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function logActivity(User $user, Student $student)
    {
        activity()
            ->performedOn($student)
            ->causedBy($user)
            ->event('student_created')
            ->withProperties([
                'matric_number' => $student->matric_number,
                'department_id' => $student->department_id,
                'year_of_admission' => $student->year_of_admission,
            ])
            ->log('Student account created');
    }
}
