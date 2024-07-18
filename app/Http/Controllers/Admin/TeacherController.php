<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    public function update(Request $request, Teacher $teacher){

    }
}
