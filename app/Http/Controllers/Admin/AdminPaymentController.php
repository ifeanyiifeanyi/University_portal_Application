<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Semester;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
