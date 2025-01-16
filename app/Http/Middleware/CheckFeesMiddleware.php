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
        // Get the authenticated student
    $student = Student::where('user_id', $this->authService->user()->id)->first();

         // Get current academic session and semester
         $currentSession = AcademicSession::where('is_current', 1)->first(); // You'll need to implement this helper
         $currentSemester = Semester::where('is_current', 1)->first(); // You'll need to implement this helper

        //  $requiredPayments = DepartmentPaymentType::join('payment_types', 'department_payment_type.payment_type_id', '=', 'payment_types.id')
        //     ->where([
        //         'department_payment_type.department_id' => $student->department_id,
        //         'department_payment_type.level' => $student->current_level,
        //         'payment_types.is_active' => true
        //     ])
        //     ->select([
        //         'department_payment_type.*',
        //         'payment_types.name',
        //         'payment_types.amount',
        //         'payment_types.payment_period',
        //         'payment_types.academic_session_id',
        //         'payment_types.semester_id'
        //     ])
        //     ->get();

        // // Check each required payment
        // foreach ($requiredPayments as $requiredPayment) {
        //     // Skip if payment is not for current period
        //     if ($requiredPayment->payment_period === 'semester') {
        //         if ($requiredPayment->semester_id && $requiredPayment->semester_id != $currentSemester->id) {
        //             continue;
        //         }
        //     } elseif ($requiredPayment->payment_period === 'session') {
        //         if ($requiredPayment->academic_session_id && $requiredPayment->academic_session_id != $currentSession->id) {
        //             continue;
        //         }
        //     }

        //     // Check if payment exists and is completed
        //     $payment = Payment::where([
        //         'student_id' => $student->id,
        //         'payment_type_id' => $requiredPayment->payment_type_id,
        //         'academic_session_id' => $currentSession->id,
        //         'semester_id' => $currentSemester->id,
        //         'department_id' => $student->department_id,
        //         'level' => $student->current_level
        //     ])->first();

        //     // If payment doesn't exist or isn't completed
        //     if (!$payment || !in_array($payment->status, ['paid', 'completed'])) {
        //         // Handle installment payments
        //         if ($payment && $payment->is_installment && $payment->installment_status === 'completed') {
        //             continue; // Payment is complete via installments
        //         }
        //         return response()->view('student.error.fees',['message'=>"Please complete payment for {$requiredPayment->name}"]);

        //     }
        // }
        $numericLevel = $student->department->getLevelNumber($student->current_level);
        $paymentTypes = DepartmentPaymentType::with(['paymentType' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('department_id', $student->department_id)
        ->where('level', $numericLevel)  // Using the converted numeric level
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
        if(count($paymentTypes) != 0){
            $message = 'Our records indicate that your fees for the current session and semester have not yet been paid. Kindly proceed with the necessary payments to avoid any disruptions to your academic activities';
        return response()->view('student.error.fees',['message'=>$message]);
        }
        return $next($request);
     }
}
