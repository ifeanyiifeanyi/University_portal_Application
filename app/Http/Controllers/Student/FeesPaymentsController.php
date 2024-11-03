<?php

namespace App\Http\Controllers\Student;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class FeesPaymentsController extends Controller
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
  //   get the student id of the user
  $student = Student::where('user_id',$this->authService->user()->id)->first();
        $payments = Payment::with(['student.user','academicSession','semester','paymentType','paymentMethod','receipt'])->where('student_id',$student->id)->get();
        // dd($payments);
        return view('student.payments.index',compact('payments'));
    }
}
