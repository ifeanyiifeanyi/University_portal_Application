<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnlineClassesController extends Controller
{
    public function index(){
        return view('student.onlineclasses.index');
    }
    public function view(){
        return view('student.onlineclasses.view');
    }
}
