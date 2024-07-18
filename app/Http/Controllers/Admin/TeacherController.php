<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function show(Teacher $teacher){
        return view('admin.lecturer.detail', compact('teacher'));
    }

    public function edit(Teacher $teacher){
        return view('admin.lecturer.edit', compact('teacher'));
    }
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        // Handle file upload
        if ($request->hasFile('profile_photo')) {
            if ($teacher->user->profile_photo) {
                Storage::delete('public/profile_photos/' . $teacher->user->profile_photo);
            }
            $profilePhotoName = $request->file('profile_photo')->store('profile_photos', 'public');
            $teacher->user->profile_photo = $profilePhotoName;
        }

        // Update user data
        $teacher->user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'phone' => $request->phone,
            'email' => $request->email,
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

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher details updated successfully.');
    }

}
