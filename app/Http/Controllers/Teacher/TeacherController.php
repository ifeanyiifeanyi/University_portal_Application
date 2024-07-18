<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }
        $teachers = Teacher::query()->latest()->get();
        return view('admin.lecturer.index', compact('teachers'));
    }
}
