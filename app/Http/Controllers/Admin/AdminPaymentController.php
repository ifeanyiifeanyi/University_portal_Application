<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\SubmitPaymentFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PaymentProcessed;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminPaymentNotification;

class AdminPaymentController extends Controller
{
    protected $paymentGatewayService;

    public function __construct(PaymentGatewayService $paymentGatewayService)
    {
        $this->paymentGatewayService = $paymentGatewayService;
    }


    // process payment for student
    public function index()
    {
        $paymentTypes = PaymentType::select('payment_types.*')
            ->distinct()
            ->with(['departments' => function ($query) {
                $query->select('departments.id', 'departments.name')
                    ->distinct();
            }])
            ->active()
            ->get();

        $paymentMethods = PaymentMethod::active()->get();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();

        return view('admin.payments.index', compact('paymentTypes', 'paymentMethods', 'academicSessions', 'semesters'));
    }

    //get department level for api(payment resquest)
    public function getDepartmentsAndLevels(Request $request)
    {
        $paymentType = PaymentType::findOrFail($request->payment_type_id);

        $currentDate = now();
        $lateFee = $paymentType->calculateLateFee($currentDate);

        $departmentsAndLevels = $paymentType->departments()->with(['paymentTypes' => function ($query) use ($paymentType) {
            $query->where('payment_types.id', $paymentType->id);
        }])->get()->map(function ($department) {
            $levels = $department->paymentTypes->pluck('pivot.level')->unique()->values();
            return [
                'id' => $department->id,
                'name' => $department->name,
                'levels' => $levels->toArray(),
            ];
        });

        return response()->json([
            'departments' => $departmentsAndLevels,
            'amount' => $paymentType->amount + $lateFee, // Include the penalty in the amount
            'late_fee' => $lateFee,
            'due_date' => $paymentType->due_date,
        ]);
    }

    public function getStudents(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:100|max:600',
            'payment_type_id' => 'required|exists:payment_types,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $paidStudentIds = Payment::where('payment_type_id', $request->payment_type_id)
            ->where('academic_session_id', $request->academic_session_id)
            ->where('semester_id', $request->semester_id)
            ->pluck('student_id');

        $students = Student::where('department_id', $request->department_id)
            ->where('current_level', $request->level)
            ->whereNotIn('id', $paidStudentIds)
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


    public function submitPaymentForm(SubmitPaymentFormRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Check if an invoice exists for the student and payment context
            $invoice = Invoice::where([
                'student_id' => $validated['student_id'],
                'payment_type_id' => $validated['payment_type_id'],
                'department_id' => $validated['department_id'],
                'level' => $validated['level'],
                'academic_session_id' => $validated['academic_session_id'],
                'semester_id' => $validated['semester_id'],
            ])->first();

            if ($invoice) {
                // Update the existing invoice
                $invoice->update([
                    'amount' => $validated['amount'],
                    'payment_method_id' => $validated['payment_method_id'],
                    'status' => 'pending', // Reset status if needed
                    'updated_at' => now(), // Update the timestamp
                ]);
            } else {
                // Create a new invoice if none exists
                $invoice = Invoice::create([
                    'student_id' => $validated['student_id'],
                    'payment_type_id' => $validated['payment_type_id'],
                    'department_id' => $validated['department_id'],
                    'level' => $validated['level'],
                    'academic_session_id' => $validated['academic_session_id'],
                    'semester_id' => $validated['semester_id'],
                    'amount' => $validated['amount'],
                    'payment_method_id' => $validated['payment_method_id'],
                    'status' => 'pending',
                    'invoice_number' => 'INV' . uniqid(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.payments.showConfirmation', $invoice->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation or update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process invoice. Please try again.');
        }
    }


    //ths is what we see before actual payment is done
    public function showConfirmation($invoiceId = null)
    {
        // Check if the invoiceId is missing or empty
        if (empty($invoiceId)) {
            // Redirect back to the form if no parameter is present
            return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
        }

        // Attempt to retrieve the invoice with related data
        $invoice = Invoice::with([
            'student.user',
            'student.department',
            'paymentType',
            'paymentMethod',
            'academicSession',
            'semester'
        ])->find($invoiceId);

        // Check if the invoice was not found
        if (is_null($invoice)) {
            // Redirect back to the form if no invoice was found
            return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
        }

        // Get all active payment methods
        $paymentMethods = PaymentMethod::active()->get();

        // Return the view with the found invoice
        return view('admin.payments.confirm', compact('invoice', 'paymentMethods'));
    }


    // option to change payment method in the invoice
    public function changePaymentMethod(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $newPaymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        $invoice->payment_method_id = $newPaymentMethod->id;
        $invoice->save();

        return response()->json([
            'success' => true,
            'isCreditCard' => $newPaymentMethod->isCreditCard()
        ]);
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

    public function processPayment(ProcessPaymentRequest $request)
    {
        $validated = $request->validated();

        //check for late fee
        $paymentType = PaymentType::findOrFail($validated['payment_type_id']);
        $currentDate = now();
        $lateFee = $paymentType->calculateLateFee($currentDate);


        // Calculate the total amount (including penalty, if any)
        $baseAmount = $paymentType->getAmount($validated['department_id'], $validated['level']);


        // Add more detailed logging for pivot data
        // $pivotData = $paymentType->departments()
        //     ->where('department_id', $validated['department_id'])
        //     ->where('level', $validated['level'])
        //     ->first();


        if (!$baseAmount) {
            return redirect()->back()->with('error', 'Payment amount could not be determined for the selected department and level.');
        }
        $totalAmountDue = $baseAmount + $lateFee;



        // Verify the submitted amount matches what we expect
        if ((float)$validated['amount'] !== (float)$totalAmountDue) {
            return redirect()->back()->with('error', 'Invalid payment amount. Expected: ' . $totalAmountDue);
        }

        DB::beginTransaction();

        try {
            // Check if payment already exists
            $existingPayment = Payment::where([
                'student_id' => $request->student_id,
                'payment_type_id' => $request->payment_type_id,
                'academic_session_id' => $request->academic_session_id,
                'semester_id' => $request->semester_id,
            ])->first();

            if ($existingPayment) {
                if ($existingPayment->status !== 'pending') {
                    DB::rollBack();
                    return redirect()->back()->withError('Payment already exists for this student.');
                }

                // Update existing pending payment
                $existingPayment->update([
                    'payment_method_id' => $validated['payment_method_id'],
                    'invoice_number' => $validated['invoice_number'],
                    'amount' => $totalAmountDue,
                    'base_amount' => $baseAmount,
                    'late_fee' => $lateFee,
                    'department_id' => $validated['department_id'],
                    'level' => $validated['level'],
                    'admin_id' => Auth::id(),
                    'transaction_reference' => 'PAY' . uniqid(),
                    'payment_date' => now()
                ]);

                $payment = $existingPayment;
            } else {
                // Create new payment if none exists
                $payment = Payment::create([
                    'student_id' => $validated['student_id'],
                    'payment_type_id' => $validated['payment_type_id'],
                    'payment_method_id' => $validated['payment_method_id'],
                    'academic_session_id' => $validated['academic_session_id'],
                    'semester_id' => $validated['semester_id'],
                    'invoice_number' => $validated['invoice_number'],
                    'amount' => $totalAmountDue,
                    'base_amount' => $baseAmount,
                    'late_fee' => $lateFee,
                    'department_id' => $validated['department_id'],
                    'level' => $validated['level'],
                    'status' => 'pending',
                    'admin_id' => Auth::id(),
                    'transaction_reference' => 'PAY' . uniqid(),
                    'payment_date' => now()
                ]);
            }

            $paymentUrl = $this->paymentGatewayService->initializePayment($payment);

            DB::commit();
            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initialization failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to initialize payment. Please try again later.');
        }
    }


    protected function sendPaymentNotification(Payment $payment)
    {
        $student = $payment->student;
        $user = $student->user;

        // Refresh the payment to ensure we have the latest data
        $payment->refresh();

        // Send email notification to student
        $user->notify(new PaymentProcessed($payment));

        // Send notification to admins and staff
        $adminsAndStaff = User::whereHas('admin', function ($query) {
            $query->whereIn('role', [Admin::TYPE_SUPER_ADMIN, Admin::TYPE_STAFF]);
        })->get();

        foreach ($adminsAndStaff as $adminUser) {
            $adminUser->notify(new AdminPaymentNotification($payment));
        }

        return true;
    }


    // public function verifyPayment(Request $request, $gateway)
    // {
    //     $reference = $request->query('reference');
    //     $admin = User::findOrFail(Auth::id());

    //     DB::beginTransaction();

    //     try {
    //         $result = $this->paymentGatewayService->verifyPayment($gateway, $reference);

    //         if ($result['success']) {
    //             $payment = Payment::where('transaction_reference', $reference)->firstOrFail();
    //             $payment->status = 'paid';
    //             $payment->admin_comment = "Credit card payment was processed by, " . $admin->full_name;
    //             $payment->save();

    //             // Update invoice status
    //             // $invoice = $payment->invoice;
    //             $invoice = Invoice::where('invoice_number', $payment->invoice_number)->first();
    //             if ($invoice) {
    //                 $invoice->status = 'paid';
    //                 $invoice->save();
    //             }
    //             // Generate payment receipt
    //             $receipt = $this->generateReceipt($payment);

    //             // Send notifications (student, admin)
    //             $this->sendPaymentNotification($payment);

    //             DB::commit();

    //             return redirect()->route('admin.payments.showReceipt', $receipt->id)
    //                 ->with('success', 'Payment verified successfully')
    //                 ->with('receipt', $receipt);
    //         } else {
    //             throw new \Exception('Payment verification failed');
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Payment verification failed: ' . $e->getMessage());
    //         return redirect()->route('admin.payments.showConfirmation')
    //             ->with('error', 'Payment verification failed. Please contact support if you believe this is an error.');
    //     }
    // }

    public function verifyPayment(Request $request, $gateway)
    {
        $reference = $request->query('reference');
        $admin = User::findOrFail(Auth::id());

        Log::info('Starting payment verification', [
            'gateway' => $gateway,
            'reference' => $reference
        ]);

        DB::beginTransaction();

        try {
            // First, find the payment before verification
            $payment = Payment::where('transaction_reference', $reference)
                ->with(['invoice', 'student.user'])
                ->firstOrFail();

            $result = $this->paymentGatewayService->verifyPayment($gateway, $reference);

            if ($result['success']) {
                // Update payment status
                $payment->status = 'paid';
                $payment->admin_comment = "Credit card payment was processed by " . $admin->full_name;
                $payment->save();

                // Update invoice status if it exists
                if ($payment->invoice) {
                    $payment->invoice->status = 'paid';
                    $payment->invoice->save();
                } else {
                    Log::warning('Invoice not found for payment', [
                        'payment_id' => $payment->id,
                        'invoice_number' => $payment->invoice_number
                    ]);
                }

                // Generate receipt
                $receipt = $this->generateReceipt($payment);

                // Send notifications
                try {
                    $this->sendPaymentNotification($payment);
                } catch (\Exception $e) {
                    Log::error('Failed to send payment notification: ' . $e->getMessage());
                    // Continue execution even if notification fails
                }

                DB::commit();

                Log::info('Payment verification successful', [
                    'payment_id' => $payment->id,
                    'receipt_id' => $receipt->id
                ]);

                return redirect()->route('admin.payments.showReceipt', $receipt->id)
                    ->with('success', 'Payment verified successfully');
            } else {
                throw new \Exception('Payment verification returned false');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Payment record not found during verification', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.payment.pay')
                ->with('error', 'Payment record not found. Please contact support if you believe this is an error.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.payment.pay')
                ->with('error', 'Payment verification failed. Please contact support if you believe this is an error.');
        }
    }

    protected function generateReceipt(Payment $payment)
    {
        // Check if receipt already exists
        $existingReceipt = Receipt::where('payment_id', $payment->id)->first();
        if ($existingReceipt) {
            return $existingReceipt;
        }

        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC' . uniqid(),
            'amount' => $payment->amount,
            'date' => now(),
        ]);
    }

    public function showReceipt(Receipt $receipt)
    {
        if (!$receipt) {
            Log::error('Attempt to view non-existent receipt');
            return redirect()->route('admin.payment.pay')
                ->with('error', 'Receipt not found');
        }

        $receipt->load([
            'payment.student.user',
            'payment.student.department',
            'payment.paymentType',
            'payment.paymentMethod',
            'payment.academicSession',
            'payment.semester'
        ]);

        return view('admin.payments.show-receipt', compact('receipt'));
    }

    // protected function generateReceipt(Payment $payment)
    // {
    //     return Receipt::create([
    //         'payment_id' => $payment->id,
    //         'receipt_number' => 'REC' . uniqid(),
    //         'amount' => $payment->amount,
    //         'date' => now(),
    //     ]);
    // }

    // public function showReceipt(Receipt $receipt)
    // {
    //     if (!$receipt) {
    //         return redirect()->route('admin.payment.pay');
    //     }
    //     return view('admin.payments.show-receipt', compact('receipt'));
    // }


    public function payTransfer($invoice)
    {
        // Check if the invoiceId is missing or empty
        if (empty($invoice)) {
            // Redirect back to the form if no parameter is present
            return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
        }

        // Attempt to retrieve the invoice with related data
        $invoice = Invoice::with([
            'student.user',
            'student.department',
            'paymentType',
            'paymentMethod',
            'academicSession',
            'semester'
        ])->find($invoice);

        // Check if the invoice was not found
        if (is_null($invoice)) {
            // Redirect back to the form if no invoice was found
            return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
        }

        return view('admin.payments.payInvoice', compact('invoice'));
    }



    public function ProcessedPayments(Request $request)
    {
        $query = Payment::with([
            'student.user',
            'student.department',
            'paymentType',
            'paymentMethod',
            'academicSession',
            'semester'
        ])->where('status', 'paid');

        // Academic Session Filter
        if ($request->filled('academic_session')) {
            $query->where('academic_session_id', $request->academic_session);
        }

        // Semester Filter
        if ($request->filled('semester')) {
            $query->where('semester_id', $request->semester);
        }

        // Department Filter
        if ($request->filled('department')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        // Date Range Filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('matric_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest()->paginate(15);
        $departments = Department::all();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();

        return view('admin.payments.list_of_paid', compact(
            'payments',
            'departments',
            'academicSessions',
            'semesters'
        ));
    }

    public function ProcessedPaymentDetails(Payment $payment)
    {
        $payment->load([
            'student.user',
            'student.department',
            'paymentType',
            'paymentMethod',
            'academicSession',
            'semester',
            'invoice',
            'receipt'
        ]);

        return view('admin.payments.paidDetails', compact('payment'));
    }
}
