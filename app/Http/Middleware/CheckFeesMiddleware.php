<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\AcademicSession;
use Symfony\Component\HttpFoundation\Response;

class CheckFeesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     protected $authService;
 
     /**
      * CLASS
      * instance of our auth service class
      */
     public function __construct(AuthService $authService){
 
         $this->authService = $authService;
     }
    public function handle(Request $request, Closure $next): Response
    {
        // check for student
        // check for session
        // check for semester

        $student = Student::where('user_id',$this->authService->user()->id)->first();
        $semester = Semester::where('is_current',1)->first();
        $session = AcademicSession::where('is_current',1)->first();

        $checkpayment = Payment::where('department_id',$student->department_id)->where('student_id',$student->id)->where('academic_session_id',$session->id)->where('semester_id',$semester->id)->where('level',$student->current_level)->where('status','paid')->first();
        if (!$checkpayment) {
        abort('403','You have not paid for the school fees for this session and semester yet (Please go ahead and make the payments)');
       }
        return $next($request);
    }
}
