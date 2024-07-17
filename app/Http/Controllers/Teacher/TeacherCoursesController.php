<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherCoursesController extends Controller
{
    public function courses(){
        return view('teacher.courses.courses');
    }
}
