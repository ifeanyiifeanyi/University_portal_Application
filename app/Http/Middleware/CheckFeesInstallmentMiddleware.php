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


            if ($nextInstallment->due_date->isPast()) {
                $message = 'You have an overdue payment of ' . number_format($nextInstallment->amount, 2) . '. Please complete it to continue.';
                return response()->view('student.error.fees',['message'=>$message]);
                // return redirect('/payment')->with('message', 'You have an overdue payment of ' . number_format($nextInstallment->amount, 2) . '. Please complete it to continue.');
            }
        }
    }
        return $next($request);
    }
}
