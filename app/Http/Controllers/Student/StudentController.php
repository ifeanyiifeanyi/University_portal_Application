<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentprofileRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Department;
use App\Services\StudentService;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        $student = Student::with('user')->where('user_id', $this->authService->user()->id)->first();
        $totalfees = Payment::where('student_id', $student->id)->sum('amount');

        return view('student.dashboard', [
            'student' => $student,
            'totalfees' => $totalfees
        ]);
    }

    public function profile()
    {
        // $getuser = User::where('id',$this->authService->user()->id)->first();
        $profile = Student::with('user')->where('user_id', $this->authService->user()->id)->first();
        $currentDepartment = Department::find($profile->department_id);
        $levels = $currentDepartment ? $currentDepartment->levels : [];
        return view('student.profile.profile', [
            'student' => $profile,
            'levels' => $levels,
            'currentDepartment' => $currentDepartment
            // 'getuser'=>$getuser
        ]);
    }

    public function createprofile(StudentprofileRequest $createstudentprofile) {}
    public function updateprofile(StudentprofileRequest $updatestudentprofile, StudentService $studentservice)
    {
        return $studentservice->updateprofile($updatestudentprofile);
    }

    public function virtualid()
    {
        // $getuser = User::where('id',$this->authService->user()->id)->first();
        $profile = Student::with(['department', 'user'])->where('user_id', $this->authService->user()->id)->first();
        return view('student.profile.virtualid', [
            'student' => $profile,
            // 'getuser'=>$getuser
        ]);
    }

    public function getStudentPaymentDashboard($student_id)
    {
        $student = Student::with('user')->where('user_id', $student_id)->first();
        $payments = Payment::with([
            'paymentMethod',
            'academicSession',
            'semester',
            'paymentType',
            'installments'
        ])
            ->where('student_id', $student->id)
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => floatval($payment->amount),
                    'payment_date' => $payment->payment_date->format('Y-m-d'),
                    'status' => $payment->status,
                    'transaction_reference' => $payment->transaction_reference,
                    'payment_method' => $payment->paymentMethod->name,
                    'is_installment' => $payment->is_installment,
                    'remaining_amount' => floatval($payment->remaining_amount),
                    'next_transaction_amount' => floatval($payment->next_transaction_amount),
                    'installment_status' => $payment->installment_status,
                    'next_installment_date' => $payment->next_installment_date ? $payment->next_installment_date->format('Y-m-d') : null,
                    'academic_session' => $payment->academicSession->name,
                    'semester' => $payment->semester->name,
                    'level' => $payment->level,
                    'payment_type' => $payment->paymentType->name,
                    'installments' => $payment->is_installment ? $payment->installments->map(function ($installment) {
                        return [
                            'amount' => floatval($installment->amount),
                            'due_date' => $installment->due_date->format('Y-m-d'),
                            'status' => $installment->status,
                            'installment_number' => $installment->installment_number
                        ];
                    }) : null
                ];
            });

        return response()->json([
            'payments' => $payments
        ]);
    }

    public function changepassword()
    {
        return view('student.profile.changepassword');
    }
    public function updatepassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|different:current_password|confirmed'
        ]);
        $user = User::where('id', $this->authService->user()->id)->first();
        $current_password = $request->current_password;

        if (Auth::attempt(['email' => $user->email, 'password' => $current_password])) {
            $user->update(['password' => bcrypt($request->password)]);

            return redirect()->back()->with('success', 'Password changed successfully');
        } else {

            return redirect()->back()->with('error', 'Current password is incorrect.');
        }
    }
}
