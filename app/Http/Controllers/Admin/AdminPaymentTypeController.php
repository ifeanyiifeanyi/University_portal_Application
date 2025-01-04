<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\Department;
use App\Models\PaymentType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminPaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentTypes = PaymentType::with('departments')->latest()->get();
        return view('admin.paymentTypes.index', compact('paymentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $academic_sessions = AcademicSession::all();
        $semesters = Semester::all();
        return view('admin.paymentTypes.create', compact(
            'departments',
            'academic_sessions',
            'semesters'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'is_recurring' => 'required|boolean',
            'due_date' => 'required|date|after:now()',
            'grace_period_days' => 'required|integer|min:0',
            'late_fee_amount' => 'required|numeric|min:0',
            'payment_period' => 'required|string',

            'paystack_subaccount_code' => 'nullable|string',
            'subaccount_percentage' => 'nullable|numeric|min:0|max:100',


            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'department_id' => 'required|exists:departments,id',
            'department_id' => 'required|exists:departments,id',
            'levels' => 'required|array',
            'levels.*' => 'required|string',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric',
            'description' => 'required|string',
        ]);
        $paymentType = PaymentType::create([
            'name' => $validated['name'],

            'is_recurring' => $validated['is_recurring'],
            'due_date' => $validated['due_date'],
            'grace_period_days' => $validated['grace_period_days'],
            'late_fee_amount' => $validated['late_fee_amount'],
            'payment_period' => $validated['payment_period'],

            'paystack_subaccount_code' => $validated['paystack_subaccount_code'],
            'subaccount_percentage' => $validated['subaccount_percentage'],

            'academic_session_id' => $validated['academic_session_id'],
            'semester_id' => $validated['semester_id'],
            'is_active' => $request->has('is_active'),
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'slug' => Str::slug($validated['name'])
        ]);



        $departmentId = $validated['department_id'];
        $department = Department::findOrFail($departmentId);
        $levels = $validated['levels'];


        foreach ($levels as $levelDisplay) {
            DB::table('department_payment_type')->insert([
                'department_id' => $departmentId,
                'payment_type_id' => $paymentType->id,
                'level' => $department->getLevelNumber($levelDisplay), // Convert to numeric
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }



        return redirect()->route('admin.payment_type.index')->with([
            'message' => 'Payment Type Created Successfully!!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentType $paymentType)
    {
        // dd($paymentType);
        return view('admin.paymentTypes.show', compact('paymentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentType $paymentType)
    {
        $departments = Department::all();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $paymentType->load('departments');
        return view('admin.paymentTypes.edit', compact('paymentType', 'departments', 'academicSessions', 'semesters'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentType $paymentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',

            'is_recurring' => 'required|boolean',
            'due_date' => 'required|date|after:now()',
            'grace_period_days' => 'required|integer|min:0',
            'late_fee_amount' => 'required|numeric|min:0',
            'payment_period' => 'required|string',

            'levels' => 'required|array',
            'levels.*' => 'required|string',
            'is_active' => 'required|boolean',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $paymentType->update([
            'name' => $validated['name'],

            'is_recurring' => $validated['is_recurring'],
            'due_date' => $validated['due_date'],
            'grace_period_days' => $validated['grace_period_days'],
            'late_fee_amount' => $validated['late_fee_amount'],
            'payment_period' => $validated['payment_period'],

            'is_active' => $request->has('is_active'),
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'slug' => Str::slug($validated['name']),
            'academic_session_id' => $validated['academic_session_id'],
            'semester_id' => $validated['semester_id'],
        ]);

        // $departmentId = $validated['department_id'];
        // $levels = $validated['levels'];

        $departmentId = $validated['department_id'];
        $department = Department::findOrFail($departmentId);
        $levels = $validated['levels'];

        // Remove existing relationships
        DB::table('department_payment_type')
            ->where('payment_type_id', $paymentType->id)
            ->where('department_id', $departmentId)
            ->delete();

        // Insert new relationships with converted level numbers
        foreach ($levels as $levelDisplay) {
            DB::table('department_payment_type')->insert([
                'department_id' => $departmentId,
                'payment_type_id' => $paymentType->id,
                'level' => $department->getLevelNumber($levelDisplay),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove any old relationships that are no longer valid
        DB::table('department_payment_type')
            ->where('payment_type_id', $paymentType->id)
            ->where('department_id', '!=', $departmentId)
            ->delete();

        return redirect()->route('admin.payment_type.index')->with([
            'message' => 'Payment Type Updated Successfully!!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();
        return redirect()->route('admin.payment_type.index')->with([
            'message' => 'Payment Type Deleted Successfully!!',
            'alert-type' => 'success'
        ]);
    }
}
