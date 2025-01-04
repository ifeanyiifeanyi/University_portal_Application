<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
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
use App\Models\PaymentInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PaymentProcessed;
use App\Services\PaymentGatewayService;
use App\Models\PaymentInstallmentConfig;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\SubmitPaymentFormRequest;
use App\Notifications\AdminPaymentNotification;
use Illuminate\Support\Facades\View;

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

    public function getDepartmentsAndLevels(Request $request)
    {
        $paymentType = PaymentType::findOrFail($request->payment_type_id);

        $currentDate = now();
        $lateFee = $paymentType->calculateLateFee($currentDate);

        $departmentsAndLevels = $paymentType->departments()
            ->with(['paymentTypes' => function ($query) use ($paymentType) {
                $query->where('payment_types.id', $paymentType->id);
            }])
            ->get()
            ->map(function ($department) {
                // Get the numeric levels from the pivot table
                $numericLevels = $department->paymentTypes->pluck('pivot.level')
                    ->unique()
                    ->values();

                // Create an array of level objects containing both numeric and display values
                $levels = $numericLevels->map(function ($numericLevel) use ($department) {
                    return [
                        'numeric' => $numericLevel,
                        'display' => $department->getDisplayLevel($numericLevel)
                    ];
                });

                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'levels' => $levels->toArray(),
                    'level_format' => $department->level_format
                ];
            });

        return response()->json([
            'departments' => $departmentsAndLevels,
            'amount' => $paymentType->amount + $lateFee,
            'late_fee' => $lateFee,
            'due_date' => $paymentType->due_date,
            'supports_installments' => $paymentType->supportsInstallments(),
            'installment_config' => $paymentType->supportsInstallments() ?
                $paymentType->installmentConfig :
                null
        ]);
    }

    public function getStudents(Request $request)
    {
        $department = Department::findOrFail($request->department_id);

        // Convert display level back to numeric if needed
        $numericLevel = $department->getLevelNumber($request->level);

        $students = Student::where('department_id', $request->department_id)
            ->where('current_level', $numericLevel)
            // Add any other conditions needed for payment eligibility
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->user->fullName(),
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

            $invoiceData = [
                'amount' => $validated['amount'],
                'payment_method_id' => $validated['payment_method_id'],
                'status' => 'pending',
                'is_installment' => $validated['is_installment'] ?? false,
                'updated_at' => now(),
            ];

            if ($invoice) {
                // Update the existing invoice
                $invoice->update($invoiceData);
            } else {
                // Create a new invoice if none exists
                $invoiceDataCreate = array_merge($invoiceData, [
                    'student_id' => $validated['student_id'],
                    'payment_type_id' => $validated['payment_type_id'],
                    'department_id' => $validated['department_id'],
                    'level' => $validated['level'],
                    'academic_session_id' => $validated['academic_session_id'],
                    'semester_id' => $validated['semester_id'],
                    'invoice_number' => 'INV' . uniqid(),
                ]);

                $invoice = Invoice::create($invoiceDataCreate);
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
        $paymentType = PaymentType::findOrFail($validated['payment_type_id']);

        // Calculate amounts including late fee
        $currentDate = now();
        $lateFee = $paymentType->calculateLateFee($currentDate);
        $baseAmount = $paymentType->getAmount($validated['department_id'], $validated['level']);

        if (!$baseAmount) {
            return redirect()->back()->with('error', 'Payment amount could not be determined.');
        }

        $totalAmountDue = $baseAmount + $lateFee;

        // Only treat as installment if checkbox was checked AND payment type supports it
        $isInstallment = $request->has('is_installment') &&
            $request->input('is_installment') == 1 &&
            $paymentType->supportsInstallments();

        DB::beginTransaction();

        try {
            // Check for existing payment
            $existingPayment = $this->findExistingPayment($validated);

            if ($existingPayment && $existingPayment->status !== 'pending') {
                DB::rollBack();
                return redirect()->back()->withError('Payment already exists for this student.');
            }

            // Get installment configuration if needed
            $paymentAmountForGateway = $totalAmountDue; // Default to full amount
            $installmentConfig = null;

            if ($isInstallment) {
                $installmentConfig = PaymentInstallmentConfig::where('payment_type_id', $paymentType->id)
                    ->where('is_active', true)
                    ->first();

                if (!$installmentConfig) {
                    throw new \Exception('No active installment configuration found for this payment type');
                }

                // Use the amount from the form instead of recalculating
                $paymentAmountForGateway = $request->input('amount');

                // Validate that the amount matches expected first installment
                $expectedFirstPayment = ($totalAmountDue * $installmentConfig->minimum_first_payment_percentage) / 100;
                if (abs($paymentAmountForGateway - $expectedFirstPayment) > 0.01) { // Allow for small floating point differences
                    throw new \Exception('Invalid installment amount provided');
                }
            }

            // Create or update payment record
            $payment = $this->createOrUpdatePayment(
                $existingPayment,
                $validated,
                $totalAmountDue,
                $baseAmount,
                $lateFee,
                $isInstallment
            );

            // Store installment details if applicable
            if ($isInstallment && $installmentConfig) {
                $payment->update([
                    'next_transaction_amount' => $paymentAmountForGateway,
                    'remaining_amount' => $totalAmountDue - $paymentAmountForGateway,
                    'next_installment_date' => now()->addDays($installmentConfig->interval_days),
                    'payment_installment_configs_id' => $installmentConfig->id
                ]);

                // Create installment records
                $this->createInstallmentRecords($payment, $installmentConfig, $totalAmountDue);
            }

            // Initialize payment with the gateway
            $paymentUrl = $this->paymentGatewayService->initializePayment(
                $payment,
                $paymentAmountForGateway
            );

            DB::commit();
            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initialization failed: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'payment_type_id' => $validated['payment_type_id'],
                'is_installment' => $isInstallment,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to initialize payment. Please try again.');
        }
    }



    protected function createInstallmentRecords(Payment $payment, PaymentInstallmentConfig $config, $totalAmount)
    {
        // Calculate base installment amount (excluding the first payment)
        $firstInstallmentAmount = ($totalAmount * $config->minimum_first_payment_percentage) / 100;
        $remainingAmount = $totalAmount - $firstInstallmentAmount;
        $remainingInstallments = $config->number_of_installments - 1;
        $baseInstallmentAmount = $remainingInstallments > 0 ? $remainingAmount / $remainingInstallments : 0;

        // Create first installment record
        PaymentInstallment::create([
            'payment_id' => $payment->id,
            'installment_number' => 1,
            'amount' => $firstInstallmentAmount,
            'paid_amount' => 0.00,
            'due_date' => now(),
            'status' => 'pending',
            'paid_at' => null
        ]);

        // Create remaining installment records
        $dueDate = now();
        for ($i = 2; $i <= $config->number_of_installments; $i++) {
            $dueDate = $dueDate->copy()->addDays($config->interval_days);

            // Check if the due date is already passed
            $status = $dueDate->isPast() ? 'overdue' : 'pending';

            PaymentInstallment::create([
                'payment_id' => $payment->id,
                'installment_number' => $i,
                'amount' => $baseInstallmentAmount,
                'paid_amount' => 0.00,
                'due_date' => $dueDate,
                'status' => $status,
                'paid_at' => null
            ]);
        }

        // Update the payment record with installment tracking
        $payment->update([
            'next_transaction_amount' => $firstInstallmentAmount,
            'remaining_amount' => $remainingAmount,
            'next_installment_date' => now(),
            'payment_installment_configs_id' => $config->id
        ]);
    }

    protected function setupInstallmentPayments(Payment $payment)
    {
        try {
            $payment->setupInstallments();
        } catch (\Exception $e) {
            Log::error('Failed to setup installment payments: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function findExistingPayment($validated)
    {
        return Payment::where([
            'student_id' => $validated['student_id'],
            'payment_type_id' => $validated['payment_type_id'],
            'academic_session_id' => $validated['academic_session_id'],
            'semester_id' => $validated['semester_id'],
        ])->first();
    }

    protected function createOrUpdatePayment($existingPayment, $validated, $totalAmount, $baseAmount, $lateFee, $isInstallment)
    {
        $paymentData = [
            'payment_method_id' => $validated['payment_method_id'],
            'invoice_number' => $validated['invoice_number'],
            'amount' => $totalAmount,
            'base_amount' => $baseAmount,
            'late_fee' => $lateFee,
            'department_id' => $validated['department_id'],
            'level' => $validated['level'],
            'admin_id' => Auth::id(),
            'transaction_reference' => 'PAY' . uniqid(),
            'payment_date' => now(),
            'is_installment' => $isInstallment,
            'status' => 'pending'
        ];

        if ($existingPayment) {
            $existingPayment->update($paymentData);
            return $existingPayment;
        }

        $paymentData = array_merge($paymentData, [
            'student_id' => $validated['student_id'],
            'payment_type_id' => $validated['payment_type_id'],
            'academic_session_id' => $validated['academic_session_id'],
            'semester_id' => $validated['semester_id'],
        ]);

        return Payment::create($paymentData);
    }



    public function verifyPayment(Request $request, $gateway)
    {
        $reference = $request->query('reference');
        $admin = User::findOrFail(Auth::id());

        DB::beginTransaction();

        try {
            $payment = Payment::where('transaction_reference', $reference)
                ->with(['invoice', 'student.user', 'installments'])
                ->firstOrFail();

            $result = $this->paymentGatewayService->verifyPayment($gateway, $reference);


            if ($result['success']) {


                // Generate receipt and send notifications
                $receipt = $this->generateReceipt($payment);
                $this->sendPaymentNotification($payment);

                DB::commit();

                return redirect()->route('admin.payments.showReceipt', $receipt->id)
                    ->with('success', 'Payment verified successfully');
            }

            throw new \Exception('Payment verification failed');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.payment.pay')
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }


    protected function handleInstallmentVerification(Payment $payment, $paidAmount)
    {
        DB::beginTransaction();
        try {
            // Get current installment record
            $currentInstallment = $payment->installments()
                ->where('status', 'pending')
                ->orderBy('installment_number')
                ->lockForUpdate()
                ->first();

            if (!$currentInstallment) {
                throw new \Exception('No pending installment found');
            }

            // Calculate penalty if due date has passed
            $penaltyAmount = 0;
            if (now()->isAfter($currentInstallment->due_date)) {
                $penaltyAmount = $currentInstallment->calculatePenalty();
            }

            // Update the current installment
            $currentInstallment->update([
                'status' => 'paid',
                'paid_amount' => $paidAmount,
                'paid_at' => now(),
            ]);

            // Update payment tracking fields
            $totalPaidSoFar = $payment->next_transaction_amount ?? 0;
            $newTotalPaid = $totalPaidSoFar + $paidAmount;
            $remainingAmount = $payment->amount - $newTotalPaid;

            // Get next installment if it exists
            $nextInstallment = $payment->installments()
                ->where('status', 'pending')
                ->orderBy('installment_number')
                ->first();

            // Update the main payment record
            $paymentUpdate = [
                'base_amount' => $newTotalPaid,
                'remaining_amount' => $remainingAmount,
                'next_transaction_amount' => null
            ];

            if ($nextInstallment) {
                $paymentUpdate['installment_status'] = 'partial';
                $paymentUpdate['next_installment_date'] = $nextInstallment->due_date;
                $paymentUpdate['next_transaction_amount'] = $nextInstallment->amount;
            } else {
                $paymentUpdate['installment_status'] = 'completed';
                $paymentUpdate['next_installment_date'] = null;
            }

            $payment->update($paymentUpdate);

            // Update invoice status if payment is complete
            if ($payment->installment_status === 'paid' && $payment->invoice) {
                $payment->invoice->update(['status' => 'paid']);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Installment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function handleFullPaymentVerification(Payment $payment, $admin)
    {
        $payment->update([
            'status' => 'paid',
            'admin_comment' => "Payment was processed by " . $admin->full_name
        ]);

        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'paid']);
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
            'amount' => $payment->base_amount,
            'date' => now(),
            'is_installment' => $payment->is_installment,
            'installment_number' => $payment->is_installment ?
                $payment->installments()->where('status', 'paid')->count() : null,
            'total_amount' => $payment->amount, // Full payment amount
            'remaining_amount' => $payment->is_installment ?
                $payment->remaining_amount : 0
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


    // paid receipts
    public function paidReceipts(Request $request)
    {
        $query = Receipt::with([
            'payment.student.user',
            'payment.academicSession',
            'payment.semester',
            // 'payment.admin'
        ]);

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        if ($request->filled('payment_type')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('is_installment', $request->payment_type === 'installment');
            });
        }

        // Filter by academic session
        if ($request->filled('academic_session')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('academic_session_id', $request->academic_session);
            });
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('semester_id', $request->semester);
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $receipts = $query->latest()->paginate(1000);
        $academicSessions = AcademicSession::orderBy('created_at', 'desc')->get();
        $semesters = Semester::all();

        return view('admin.payments.paidReceiptsList', compact('receipts', 'academicSessions', 'semesters'));
    }

    // TODO: This is incomplete( subccount payments)
    // ! getting empty array from the api
    public function getSubaccountTransactions(Request $request)
    {
        $paymentTypes = PaymentType::whereNotNull('paystack_subaccount_code')
            ->where('is_active', true)
            ->get();

        $selectedPaymentType = null;
        $transactions = [];
        $error = null;

        if ($request->has('payment_type')) {
            $selectedPaymentType = PaymentType::findOrFail($request->payment_type);

            // Log the subaccount code being used
            Log::info('Fetching transactions for payment type', [
                'payment_type_id' => $selectedPaymentType->id,
                'subaccount_code' => $selectedPaymentType->paystack_subaccount_code
            ]);

            $response = $this->paymentGatewayService->getSubaccountTransactionsPaystack(
                $selectedPaymentType->paystack_subaccount_code
            );

            // Log the response
            Log::info('Paystack service response', [
                'status' => $response['status'],
                'data_count' => count($response['data']),
                'error' => $response['error'] ?? null
            ]);

            if ($response['status']) {
                $transactions = $response['data'];
            } else {
                $error = $response['error'] ?? 'Failed to fetch transactions';
            }
        }
        return view('admin.payments.apiSubaccountPayments', compact(
            'paymentTypes',
            'selectedPaymentType',
            'transactions',
            'error'
        ));
    }
}
