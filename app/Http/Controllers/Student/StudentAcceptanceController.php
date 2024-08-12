<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class StudentAcceptanceController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function index(){
        return view('student.acceptance.index');
    }
    public function view(){
        return view('student.acceptance.view');
    }
}
