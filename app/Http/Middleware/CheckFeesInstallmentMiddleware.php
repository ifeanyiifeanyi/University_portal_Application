<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Semester;
use App\Services\AuthService;
use App\Models\AcademicSession;
use Symfony\Component\HttpFoundation\Response;

class CheckFeesInstallmentMiddleware
{
    protected $authService;
 
    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check for student
        // check for session
        // check for semester

    //     $student = Student::where('user_id',$this->authService->user()->id)->first();
    //     $semester = Semester::where('is_current',1)->first();
    //     $session = AcademicSession::where('is_current',1)->first();

    //     $checkpayment = Payment::where('department_id',$student->department_id)->where('student_id',$student->id)->where('academic_session_id',$session->id)->where('semester_id',$semester->id)->where('level',$student->current_level)->where('status','paid')->first();
    //     if (!$checkpayment) {
    //         return response()->view('student.error.fees');
    //    }

    // Get the authenticated student
    $student = Student::where('user_id', $this->authService->user()->id)->first();

    // Get the current semester and academic session
    $semester = Semester::where('is_current', 1)->first();
    $session = AcademicSession::where('is_current', 1)->first();

    // Check if the student has unpaid installments for the current semester and session
    $payment = Payment::where('department_id',$student->department_id)
        ->where('semester_id', $semester->id)
        ->where('academic_session_id', $session->id)
        ->where('status', '!=', 'paid')
        ->where('installment_status', '!=', 'completed')
        ->latest()
        ->first();

    if ($payment) {
        $nextInstallment = $payment->installments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        if ($nextInstallment) {
            // if ($nextInstallment->due_date->isFuture()) {
            //     $message = 'Your next installment is due on ' . $nextInstallment->due_date->format('Y-m-d') . '';
            //     return response()->view('student.error.fees',['message'=>$message]);
            //     // return redirect('/dashboard')->with('message', 'Your next installment is due on ' . $nextInstallment->due_date->format('Y-m-d') . '.');
            // }

            if ($nextInstallment->due_date->isPast()) {
                $message = 'You have an overdue payment of ' . number_format($nextInstallment->amount, 2) . '. Please complete it to continue.';
                return response()->view('student.error.fees',['message'=>$message]);
                // return redirect('/payment')->with('message', 'You have an overdue payment of ' . number_format($nextInstallment->amount, 2) . '. Please complete it to continue.');
            }
        }
    }else{
        $message = 'Our records indicate that your fees for the current session and semester have not yet been paid. Kindly proceed with the necessary payments to avoid any disruptions to your academic activities';
        return response()->view('student.error.fees',['message'=>$message]);
    }
        return $next($request);
    }
}
