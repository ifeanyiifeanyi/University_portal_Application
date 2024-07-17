<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherDepartmentController extends Controller
{
    public function departments(){
        return view('teacher.departments.departments');
    }
}
