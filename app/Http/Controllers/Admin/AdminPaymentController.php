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


    // public function submitPaymentForm(Request $request)
    // {
    //     $validated = $request->validate([
    //         'academic_session_id' => 'required|exists:academic_sessions,id',
    //         'semester_id' => 'required|exists:semesters,id',
    //         'payment_type_id' => 'required|exists:payment_types,id',
    //         'department_id' => 'required|exists:departments,id',
    //         'level' => 'required|numeric|min:100|max:600',
    //         'student_id' => 'required|exists:students,id',
    //         'payment_method_id' => 'required|exists:payment_methods,id',
    //         'amount' => 'required|numeric|min:0',
    //     ]);

    //     $invoice = Invoice::create([
    //         'student_id' => $validated['student_id'],
    //         'payment_type_id' => $validated['payment_type_id'],
    //         'department_id' => $validated['department_id'],
    //         'level' => $validated['level'],
    //         'academic_session_id' => $validated['academic_session_id'],
    //         'semester_id' => $validated['semester_id'],
    //         'amount' => $validated['amount'],
    //         'payment_method_id' => $validated['payment_method_id'],
    //         'status' => 'pending',
    //         'invoice_number' => 'INV-' . uniqid(),
    //     ]);

    //     return redirect()->route('admin.payments.showConfirmation', $invoice->id);
    // }

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
        ]);

        // checking if its an existing invoice
        // $pendingInvoice = Invoice::findPendingInvoice(
        //     $validated['student_id'],
        //     $validated['payment_type_id'],
        //     $validated['academic_session_id'],
        //     $validated['semester_id']
        // );
        // // dd($pendingInvoice);

        // if ($pendingInvoice) {
        //     // if true update, proceed to pay or just print
        //     $invoice = $pendingInvoice;
        //     $invoice->update([
        //         'department_id' => $validated['department_id'],
        //         'level' => $validated['level'],
        //         'amount' => $validated['amount'],
        //         'payment_method_id' => $validated['payment_method_id'],
        //     ]);
        // }
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


        return redirect()->route('admin.payments.showConfirmation', $invoice->id);
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

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|numeric|min:100|max:600',
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'invoice_number' => 'required|exists:invoices,invoice_number',
        ]);
        // $invoice = Invoice::findOrFail($validated['invoice_id']);
        // $paymentMethod = $invoice->paymentMethod;

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'student_id' => $validated['student_id'],
                'payment_type_id' => $validated['payment_type_id'],
                'payment_method_id' => $validated['payment_method_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'semester_id' => $validated['semester_id'],
                'invoice_number' => $validated['invoice_number'],
                'amount' => $request->amount,
                'department_id' => $validated['department_id'],
                'level' => $validated['level'],
                'status' => 'pending',
                'admin_id' => Auth::id(),
                'transaction_reference' => 'PAY' . uniqid(),
                'payment_date' => now()
            ]);

            $paymentUrl = $this->paymentGatewayService->initializePayment($payment);
            // dd($paymentUrl);

            DB::commit();

            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initialization failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to initialize payment. Please try again later.');
        }
    }

    // protected function sendPaymentNotification(Payment $payment)
    // {
    //     $student = $payment->student;
    //     $user = $student->user;

    //     // Send email notification to student
    //     Notification::send($user, new PaymentProcessed($payment));

    //     // Create database notification for student
    //     $user->notify(new PaymentProcessed($payment));

    //     // Send notification to admins and staff
    //     $adminsAndStaff = Admin::whereIn('role', [Admin::TYPE_SUPER_ADMIN, Admin::TYPE_STAFF])->get();
    //     Notification::send($adminsAndStaff, new AdminPaymentNotification($payment));
    // }

    protected function sendPaymentNotification(Payment $payment)
    {
        $student = $payment->student;
        $user = $student->user;

        // Send email notification to student
        $user->notify(new PaymentProcessed($payment));

        // Send notification to admins and staff
        $adminsAndStaff = Admin::whereIn('role', [Admin::TYPE_SUPER_ADMIN, Admin::TYPE_STAFF])->get();
        // dd($adminsAndStaff);

        foreach ($adminsAndStaff as $admin) {
            $admin->user->notify(new AdminPaymentNotification($payment));
        }

        return true;
    }


    public function verifyPayment(Request $request, $gateway)
    {
        $reference = $request->query('reference');
        $admin = User::findOrFail(Auth::id());

        DB::beginTransaction();

        try {
            $result = $this->paymentGatewayService->verifyPayment($gateway, $reference);

            if ($result['success']) {
                $payment = Payment::where('transaction_reference', $reference)->firstOrFail();
                $payment->status = 'paid';
                $payment->admin_comment = "Credit card payment was processed by, " . $admin->full_name;
                $payment->save();

                // Update invoice status
                // $invoice = $payment->invoice;
                $invoice = Invoice::where('invoice_number', $payment->invoice_number)->first();
                if ($invoice) {
                    $invoice->status = 'paid';
                    $invoice->save();
                }
                // Generate payment receipt
                $receipt = $this->generateReceipt($payment);

                // Send notifications (student, admin)
                // $this->sendPaymentNotification($payment);

                DB::commit();

                return redirect()->route('admin.payments.showReceipt', $receipt->id)
                    ->with('success', 'Payment verified successfully')
                    ->with('receipt', $receipt);
            } else {
                throw new \Exception('Payment verification failed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->route('admin.payments.showConfirmation')
                ->with('error', 'Payment verification failed. Please contact support if you believe this is an error.');
        }
    }

    protected function generateReceipt(Payment $payment)
    {
        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC' . uniqid(),
            'amount' => $payment->amount,
            'date' => now(),
        ]);
    }

    public function showReceipt(Receipt $receipt)
    {
        return view('admin.payments.show-receipt', compact('receipt'));
    }
}
