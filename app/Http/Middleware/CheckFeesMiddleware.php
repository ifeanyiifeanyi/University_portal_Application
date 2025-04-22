<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\PaymentType;
use App\Models\DepartmentPaymentType;
use App\Models\AcademicSession;
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
  

    public function handle(Request $request, Closure $next)
    {
        // Get the authenticated student
        $student = Student::where('user_id', $this->authService->user()->id)->first();
        
        // Get current academic session and semester
        $currentSession = AcademicSession::where('is_current', 1)->first();
        $currentSemester = Semester::where('is_current', 1)->first();
        
        $numericLevel = $student->department->getLevelNumber($student->current_level);
        
        $slug = [
            'tuition-fee-rn-second-year',
            'tuition-fee-nd-second-year',
            'tuition-fee-rn-third-year',
        ];
    
        // Get all unpaid payment types for the student's department and level
        $paymentTypes = DepartmentPaymentType::with(['paymentType' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('department_id', $student->department_id)
        ->where('level', $numericLevel)
        ->whereHas('paymentType', function($query) {
            $query->where('is_active', true);
        })
        ->whereNotExists(function ($query) use ($student) {
            $query->select('payments.id')
                ->from('payments')
                ->whereColumn('department_payment_type.payment_type_id', 'payments.payment_type_id')
                ->where('payments.student_id', $student->id);
        })
        ->get();
        
    
        // Check if there are any unpaid fees
        if(count($paymentTypes) != 0) {
            // Check if student has paid tuition fees specifically
            $hasPaidTuition = Payment::where('student_id', $student->id)
                ->whereHas('paymentType', function($query) use ($slug) {  // Note the use($slug) here
                    $query->whereIn('slug', $slug)  // Changed to whereIn since you're checking against multiple values
                          ->where('is_active', true);
                })
                ->exists();
    
            // If student hasn't paid tuition fees, block access
            if(!$hasPaidTuition) {
                $message = 'Our records indicate that your tuition fees for the current session and semester have not yet been paid. Kindly proceed with the necessary payments to avoid any disruptions to your academic activities';
                return response()->view('student.error.fees', ['message' => $message]);
            }
            
            // If student has paid tuition fees but has other unpaid fees, allow access with warning
            // You could optionally add a flash message here to notify them of other unpaid fees
        }
        
        return $next($request);
    }
}
