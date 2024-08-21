<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\SemesterCourseRegistration;

class AdminStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentsWithUsers = Student::with('user')->latest()->get();

        // Filter out students with null users
        $students = $studentsWithUsers->filter(function ($student) {
            return $student->user !== null;
        });

        // If you want to see what's going on, you can dd here:
        // dd($studentsWithUsers);

        return view('admin.student.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::query()->latest()->get();
        return view('admin.student.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'state_of_origin' => 'required|string|max:255',
            'lga_of_origin' => 'required|string|max:255',
            'hometown' => 'required|string|max:255',
            'residential_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'marital_status' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'genotype' => 'required|string|max:255',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:255',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:255',
            'jamb_registration_number' => 'nullable|string|max:255',
            'year_of_admission' => 'required|digits:4',
            'mode_of_entry' => 'required|in:UTME,Direct Entry,Transfer',
            'current_level' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        DB::beginTransaction();

        try {
            $matNumber = $this->generateMatricNumber($request->department_id);
            // Create user
            $user = User::create([
                'user_type' => User::TYPE_STUDENT,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->first_name . '.' . $request->last_name,
                'slug' => Str::slug($request->first_name . '.' . $request->last_name),
                'other_name' => $request->other_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo');
                $extension = $profilePhoto->getClientOriginalExtension();
                $profilePhotoName = time() . "." . $extension;
                $profilePhoto->move('admin/students/profile/', $profilePhotoName);
                $user->profile_photo = 'admin/students/profile/' . $profilePhotoName;
                $user->save();
            }

            // Create student
            $student = Student::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'matric_number' => $matNumber,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'state_of_origin' => $request->state_of_origin,
                'lga_of_origin' => $request->lga_of_origin,
                'hometown' => $request->hometown,
                'residential_address' => $request->residential_address,
                'permanent_address' => $request->permanent_address,
                'nationality' => $request->nationality,
                'marital_status' => $request->marital_status,
                'religion' => $request->religion,
                'blood_group' => $request->blood_group,
                'genotype' => $request->genotype,
                'next_of_kin_name' => $request->next_of_kin_name,
                'next_of_kin_relationship' => $request->next_of_kin_relationship,
                'next_of_kin_phone' => $request->next_of_kin_phone,
                'next_of_kin_address' => $request->next_of_kin_address,
                'jamb_registration_number' => $request->jamb_registration_number,
                'year_of_admission' => $request->year_of_admission,
                'mode_of_entry' => $request->mode_of_entry,
                'current_level' => $request->current_level,
            ]);

            DB::commit();
            $notification = [
                'message' => 'Student account created successfully.',
                'alert-type' => 'success'
            ];

            return redirect()->route('admin.student.view')->with($notification);
        } catch (\Exception $e) {
            $notification = [
                'message' => 'An error occurred while creating the student account. Please try again.' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            DB::rollback();
            return back()->with($notification);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return view('admin.student.details', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $departments = Department::all(); // Assuming you have a Department model
        return view('admin.student.edit', compact('student', 'departments'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'state_of_origin' => 'required|string|max:255',
            'lga_of_origin' => 'required|string|max:255',
            'hometown' => 'required|string|max:255',
            'residential_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'marital_status' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'genotype' => 'required|string|max:255',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:255',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:255',
            'jamb_registration_number' => 'nullable|string|max:255',
            'year_of_admission' => 'required|digits:4',
            'mode_of_entry' => 'required|in:UTME,Direct Entry,Transfer',
            'current_level' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // 2MB Max

        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile('profile_photo')) {
                // Get the old image path
                $old_image = $student->user->profile_photo;

                // Delete the old image if it exists
                if (!empty($old_image) && file_exists(public_path($old_image))) {
                    unlink(public_path($old_image));
                }

                // Handle the new image upload
                $thumb = $request->file('profile_photo');
                $extension = $thumb->getClientOriginalExtension();
                $profilePhoto = time() . "." . $extension;
                $thumb->move('admin/students/profile/', $profilePhoto);
                $student->user->profile_photo = 'admin/students/profile/' . $profilePhoto;
                $student->user->save();
            }

            // Update user data
            $student->user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'other_name' => $request->other_name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
            // Update student data
            $student->update([
                'department_id' => $validatedData['department_id'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'gender' => $validatedData['gender'],
                'state_of_origin' => $validatedData['state_of_origin'],
                'lga_of_origin' => $validatedData['lga_of_origin'],
                'hometown' => $validatedData['hometown'],
                'residential_address' => $validatedData['residential_address'],
                'permanent_address' => $validatedData['permanent_address'],
                'nationality' => $validatedData['nationality'],
                'marital_status' => $validatedData['marital_status'],
                'religion' => $validatedData['religion'],
                'blood_group' => $validatedData['blood_group'],
                'genotype' => $validatedData['genotype'],
                'next_of_kin_name' => $validatedData['next_of_kin_name'],
                'next_of_kin_relationship' => $validatedData['next_of_kin_relationship'],
                'next_of_kin_phone' => $validatedData['next_of_kin_phone'],
                'next_of_kin_address' => $validatedData['next_of_kin_address'],
                'jamb_registration_number' => $validatedData['jamb_registration_number'],
                'year_of_admission' => $validatedData['year_of_admission'],
                'mode_of_entry' => $validatedData['mode_of_entry'],
                'current_level' => $validatedData['current_level'],
            ]);

            DB::commit();
            $notification = [
                'message' => 'Student account Updated successfully.',
                'alert-type' => 'success'
            ];

            return redirect()->route('admin.student.view')->with($notification);
        } catch (\Exception $e) {
            $notification = [
                'message' => 'An error occurred while updating the student account. Please try again.' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            DB::rollback();
            return back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $student->delete();  // This will soft delete the student
            $student->user->delete();  // This will soft delete the associated user
        });

        $notification = [
            'message' => 'Student account deleted successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.student.view')->with($notification);
    }

    private function generateMatricNumber($departmentId)
    {
        $schoolCode = 'SHN'; // School code
        $year = date('y'); // Last two digits of the current year
        $department = Department::findOrFail($departmentId);
        $departmentCode = $department->code;

        // Get the latest student number for this year
        $latestStudent = Student::where('year_of_admission', date('Y'))
            ->latest('matric_number')
            ->first();

        if ($latestStudent) {
            // Extract the last 4 digits and increment
            $lastNumber = intval(substr($latestStudent->matric_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            // If no students yet this year, start from 1
            $newNumber = 1;
        }

        // Generate the matric number
        return sprintf("%s/%s/%s/%04d", $schoolCode, $departmentCode, $year, $newNumber);
    }


    public function studentRegistrationHistory($studentId)
    {
        $student = Student::findOrFail($studentId);

        $registrationHistory = SemesterCourseRegistration::where('student_id', $studentId)
            ->with(['academicSession', 'semester', 'courseEnrollments.course', 'courseEnrollments'])
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get();

        return view('admin.student.registration_history', compact('student', 'registrationHistory'));
    }
}
