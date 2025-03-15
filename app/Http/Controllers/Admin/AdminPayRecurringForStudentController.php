<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPayRecurringForStudentRequest;
use App\Models\RecurringPaymentPlan;
use App\Models\StudentRecurringSubscription;
use App\Services\PaystackRecurringService;
use App\Services\RecurringPaymentAdminService;

class AdminPayRecurringForStudentController extends Controller
{
    public function __construct(
        private RecurringPaymentAdminService $service,
        private PaystackRecurringService $paystackService
    ) {}

    public function getRecurringPayments(){
        $recurring_payments = StudentRecurringSubscription::all();
        return view('admin.payments.recurring_payment.paid', compact('recurring_payments'));
    }

    public function index()
    {

        $plans =    RecurringPaymentPlan::active()->get();
        $departments = Department::query()->get();
        return view('admin.payments.recurring_payment.pay_for_student', compact('plans', 'departments'));
    }



    public function getStudents(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level' => 'required'
        ]);

        $students = $this->service->getStudentsByDepartmentAndLevel(
            $request->department_id,
            $request->level
        );

        return response()->json($students);
    }



    public function calculatePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:recurring_payment_plans,id',
            'number_of_payments' => 'required|integer|min:1|max:12'
        ]);

        $calculation = $this->service->calculateRecurringPayment(
            $request->number_of_payments,
            $request->plan_id
        );

        return response()->json($calculation);
    }

    public function store(Request $request)
    {

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'plan_id' => 'required|exists:recurring_payment_plans,id',
            'number_of_payments' => 'required|integer|min:1|max:12',
            'payment_method' => 'required|in:online,bank_transfer,cash'
        ]);

        $student = Student::findOrFail($request->student_id);

        $subscription = $this->service->createSubscription(
            $student,
            $request->plan_id,
            $request->number_of_payments,
            $request->payment_method
        );

         // For online payments, redirect to Paystack
         if ($request->payment_method === 'online') {
            $paymentResponse = $this->paystackService->initiatePayment(
                $subscription,
                $request->payment_method
            );

            if ($paymentResponse['status']) {
                return redirect($paymentResponse['data']['authorization_url']);
            }

            return back()->with('error', 'Could not initialize payment');
        }

        return redirect()->route('admin.recurring-payments.show', $subscription->id)
            ->with('success', 'Payment subscription created successfully');
    }

    public function getDepartmentLevel($id)
    {
        $department = Department::findOrFail($id);
        return response()->json([
            'id' => $department->id,
            'level_format' => $department->level_format,
            'duration' => $department->duration
        ]);
    }
}
