<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        return view('student.dashboard');
    }
}
