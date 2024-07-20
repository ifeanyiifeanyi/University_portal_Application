<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateTeacherRequest;

class TeacherController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }
        $teachers = Teacher::query()->latest()->get();
        return view('admin.lecturer.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.lecturer.store');
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'teaching_experience' => 'required|integer',
            'teacher_type' => 'required|string',
            'teacher_qualification' => 'required|string|max:255',
            'teacher_title' => 'required|string|max:255',
            'office_hours' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'certifications' => 'nullable|array',
            'publications' => 'nullable|array',
            'number_of_awards' => 'nullable|integer',
            'date_of_employment' => 'required|date',
            'address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'username' => $request->first.'.'.$request->last_name,
            'slug' => Str::slug($request->first.'.'.$request->last_name),
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make('12345678'), // Change this to a generated password if needed
        ]);

        $teacher = new Teacher([
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'teaching_experience' => $request->teaching_experience,
            'teacher_type' => $request->teacher_type,
            'teacher_qualification' => $request->teacher_qualification,
            'teacher_title' => $request->teacher_title,
            'office_hours' => $request->office_hours,
            'office_address' => $request->office_address,
            'biography' => $request->biography,
            'certifications' => $request->certifications ? json_encode($request->certifications) : null,
            'publications' => $request->publications ? json_encode($request->publications) : null,
            'number_of_awards' => $request->number_of_awards,
            'date_of_employment' => $request->date_of_employment,
            'address' => $request->address,
            'nationality' => $request->nationality,
            'level' => $request->level,
            'employment_id' => str_shuffle(mt_rand(1000000, 9999999))
        ]);

        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo');
            $extension = $profilePhoto->getClientOriginalExtension();
            $profilePhotoName = time() . "." . $extension;
            $profilePhoto->move('admin/lecturers/profile/', $profilePhotoName);
            $user->profile_photo = 'admin/lecturers/profile/' . $profilePhotoName;
            $user->save();
        }

        $user->teacher()->save($teacher);

        return redirect()->route('admin.teacher.view')->with([
            'message' => 'Lecturer account created successfully.',
            'alert-type' => 'success'
        ]);
    }


    public function show(Teacher $teacher)
    {
        return view('admin.lecturer.detail', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.lecturer.edit', compact('teacher'));
    }
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        // Check if a new profile photo has been uploaded
        if ($request->hasFile('profile_photo')) {
            // Get the old image path
            $old_image = $teacher->user->profile_photo;

            // Delete the old image if it exists
            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            // Handle the new image upload
            $thumb = $request->file('profile_photo');
            $extension = $thumb->getClientOriginalExtension();
            $profilePhoto = time() . "." . $extension;
            $thumb->move('admin/lecturers/profile/', $profilePhoto);
            $teacher->user->profile_photo = 'admin/lecturers/profile/' . $profilePhoto;
        }

        // Update user data
        $teacher->user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'profile_photo' => $teacher->user->profile_photo ?? $teacher->user->profile_photo,
        ]);

        // Update teacher data
        $teacher->update([
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'teaching_experience' => $request->teaching_experience,
            'teacher_type' => $request->teacher_type,
            'teacher_qualification' => $request->teacher_qualification,
            'teacher_title' => $request->teacher_title,
            'office_hours' => $request->office_hours,
            'office_address' => $request->office_address,
            'biography' => $request->biography,
            'certifications' => $request->certifications,
            'publications' => $request->publications,
            'number_of_awards' => $request->number_of_awards,
            'date_of_employment' => $request->date_of_employment,
            'address' => $request->address,
            'nationality' => $request->nationality,
            'level' => $request->level,
        ]);

        $notification = [
            'message' => 'Teacher details updated successfully.',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    public function destroy(Teacher $teacher)
    {
        // Delete the teacher
        $teacher->delete();

        // Delete the user associated with the teacher
        $teacher->user->delete();

        $notification = [
            'message' => 'Teacher deleted successfully.',
            'alert-type' => 'danger'
        ];

        return redirect()->back()->with($notification);
    }
}
