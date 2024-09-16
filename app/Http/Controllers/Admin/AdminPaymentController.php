<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;

class AdminPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentTypes = PaymentType::with('departments')->active()->get();
        $paymentMethods = PaymentMethod::active()->get();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();

        return view('admin.payments.index', compact('paymentTypes', 'paymentMethods', 'academicSessions', 'semesters'));
    }

    public function getDepartmentsAndLevels(Request $request)
    {
        $paymentType = PaymentType::findOrFail($request->payment_type_id);
        $departmentsAndLevels = $paymentType->departments()->with(['paymentTypes' => function ($query) use ($paymentType) {
            $query->where('payment_types.id', $paymentType->id);
        }])->get()->map(function ($department) {
            $levels = $department->paymentTypes->pluck('pivot.level')->unique()->values();
            return [
                'id' => $department->id,
                'name' => $department->name,
                'levels' => $levels->toArray()
            ];
        });

        return response()->json([
            'departments' => $departmentsAndLevels,
            'amount' => $paymentType->amount
        ]);
    }

    public function getStudents(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:100|max:600',
        ]);

        $students = Student::where('department_id', $request->department_id)
            ->where('current_level', $request->level)
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->user->first_name . ' ' . $student->user->last_name . ' ' . $student->user->other_name,
                    'matric_number' => $student->matric_number
                ];
            });

        return response()->json($students);
    }

    public function getAmount(Request $request)
    {
        $paymentType = PaymentType::findOrFail($request->payment_type_id);
        return response()->json(['amount' => $paymentType->amount]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:100|max:600',
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $payment = Payment::create($request->all());

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment
        ]);
    }

    public function submitPaymentForm(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|numeric|min:100|max:600',
            'student_id' => 'required|exists:students,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
            // 'transaction_reference' => 'nullable|string|max:255',
            // 'description' => 'nullable|string|max:255',
            // 'comment' => 'nullable|string|max:255',
            // 'proof_of_payment' => 'nullable|file|max:2048',
        ]);

        // Retrieve all necessary data
        $academicSession = AcademicSession::findOrFail($validated['academic_session_id']);
        $semester = Semester::findOrFail($validated['semester_id']);
        $paymentType = PaymentType::findOrFail($validated['payment_type_id']);
        $department = Department::findOrFail($validated['department_id']);
        $student = Student::with('user', 'department')->findOrFail($validated['student_id']);

        $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
        $amount = $validated['amount'];
        $level = $validated['level'];

        // Store data in session for use in confirmation page
        session([
            'payment_data' => [
                'academic_session' => $academicSession,
                'semester' => $semester,
                'payment_type' => $paymentType,
                'department' => $department,
                'level' => $validated['level'],
                'student' => $student,
                'payment_method' => $paymentMethod,
                'amount' => $validated['amount'],
                // 'transaction_reference' => $validated['transaction_reference'],
                // 'description' => $validated['description'],
                // 'comment' => $validated['comment'],
                // 'proof_of_payment' => $validated['proof_of_payment'],
            ]
        ]);

        // Redirect to confirmation page
        // return view('admin.payments.confirm', compact(
        //     'paymentType',
        //     'student',
        //     'academicSession',
        //     'semester',
        //     'paymentMethod',
        //     'department',
        //     'amount',
        //     'level'
        // ));

        return redirect(route('admin.payments.showConfirmation'));
    }

    public function generateTicket(Request $request)
    {
        $validated = $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'student_id' => 'required|exists:students,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $paymentType = PaymentType::findOrFail($validated['payment_type_id']);
        $student = Student::with('user', 'department')->findOrFail($validated['student_id']);
        $academicSession = AcademicSession::findOrFail($validated['academic_session_id']);
        $semester = Semester::findOrFail($validated['semester_id']);

        return view('admin.payments.printable-invoice', compact('paymentType', 'student', 'academicSession', 'semester'));
    }

    public function showConfirmation()
    {
        // Retrieve data from session
        $paymentData = session('payment_data');

        if (!$paymentData) {
            return redirect()->route('admin.payment.pay')->with('error', 'Payment data not found. Please start over.');
        }

        return view('admin.payments.confirm', $paymentData);
    }


    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
