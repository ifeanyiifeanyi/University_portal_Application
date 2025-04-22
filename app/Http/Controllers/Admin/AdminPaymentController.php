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

    // public function payTransfer($invoice)
    // {
    //     // Check if the invoiceId is missing or empty
    //     if (empty($invoice)) {
    //         return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
    //     }

    //     // Retrieve the invoice with related data
    //     $invoice = Invoice::with([
    //         'student.user',
    //         'student.department',
    //         'paymentType',
    //         'paymentMethod',
    //         'academicSession',
    //         'semester'
    //     ])->find($invoice);

    //     // Check if the invoice was not found
    //     if (is_null($invoice)) {
    //         return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
    //     }

    //     // Find related payment if it exists
    //     $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();

    //     $installments = null;
    //     $pendingInstallment = null;

    //     // If payment exists and is an installment type, load installments
    //     if ($payment && $payment->is_installment) {
    //         $installments = PaymentInstallment::where('payment_id', $payment->id)
    //             ->orderBy('installment_number')
    //             ->get();

    //         // Find the first pending installment
    //         $pendingInstallment = $installments->where('status', 'pending')->first();
    //     }

    //     return view('admin.payments.payInvoice', compact('invoice', 'payment', 'installments', 'pendingInstallment'));
    // }

     /**
     * Display the payment verification page for an invoice
     *
     * @param int $invoice Invoice ID
     * @return \Illuminate\Http\Response
     */
    public function payTransfer($invoice)
    {
        // Check if the invoiceId is missing or empty
        if (empty($invoice)) {
            return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
        }

        // Retrieve the invoice with related data
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
            return redirect()->route('admin.payment.pay')->with('error', 'Invoice not found. Please try again.');
        }

        // Find related payment if it exists
        $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();

        // Initialize variables
        $installments = collect();
        $pendingInstallments = collect();
        $completedInstallments = collect();
        $currentInstallmentStatus = null;
        $nextInstallmentDue = null;
        $totalPaid = 0;
        $remainingAmount = 0;
        $nextInstallment = null;

        // If payment exists and is an installment type, load installments with detailed information
        if ($payment && $payment->is_installment) {
            // Load all installments ordered by installment number
            $installments = PaymentInstallment::where('payment_id', $payment->id)
                ->orderBy('installment_number')
                ->get();

            // Separate pending and completed installments
            $completedInstallments = $installments->where('status', 'paid');
            $pendingInstallments = $installments->whereIn('status', ['pending', 'overdue']);

            // Calculate total paid and remaining amounts
            $totalPaid = $completedInstallments->sum('paid_amount');
            $remainingAmount = $payment->amount - $totalPaid;

            // Determine the overall status of the installment process
            if ($pendingInstallments->isEmpty()) {
                $currentInstallmentStatus = 'completed';
            } else {
                $currentInstallmentStatus = 'partial';
                // Get the next pending installment
                $nextInstallment = $pendingInstallments->sortBy('installment_number')->first();
                $nextInstallmentDue = $nextInstallment ? $nextInstallment->due_date : null;
            }
        }

        return view('admin.payments.payInvoice', compact(
            'invoice',
            'payment',
            'installments',
            'pendingInstallments',
            'completedInstallments',
            'totalPaid',
            'remainingAmount',
            'currentInstallmentStatus',
            'nextInstallmentDue',
            'nextInstallment'
        ));
    }


    public function ProcessedPayments(Request $request)
    {
        // Base query excluding pending payments
        $query = Payment::where('status', '!=', 'pending')
            ->with([
                'student.user',
                'student.department',
                'paymentType',
                'academicSession',
                'semester'
            ]);

        // Apply filters with precise conditions
        if ($request->filled('department')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('current_level', $request->level);
            });
        }

        if ($request->filled('academic_session')) {
            $query->where('academic_session_id', $request->academic_session);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type_id', $request->payment_type);
        }

        // Fetch filtered non-pending payments for calculations
        $filteredPayments = $query->get();

        // Comprehensive statistics calculation
        $totalStats = [
            'total_amount' => $filteredPayments->sum('amount'),
            'total_base_amount' => $filteredPayments->sum('base_amount'),
            'total_late_fee' => $filteredPayments->sum('late_fee'),
            'payments_count' => $filteredPayments->count()
        ];

        // Department-level statistics with precise filtering
        $departmentStats = Department::all()->flatMap(function ($department) use ($filteredPayments) {
            $departmentPayments = $filteredPayments->filter(function ($payment) use ($department) {
                return $payment->student && $payment->student->department_id === $department->id;
            });

            $stats = [
                [
                    'department_name' => $department->name,
                    'level' => 'All Levels',
                    'total_amount' => $departmentPayments->sum('amount'),
                    'base_amount' => $departmentPayments->sum('base_amount'),
                    'late_fee' => $departmentPayments->sum('late_fee'),
                    'count' => $departmentPayments->count()
                ]
            ];

            foreach ($department->levels as $level) {
                $numericLevel = $department->getLevelNumber($level);
                $levelPayments = $departmentPayments->filter(function ($payment) use ($numericLevel) {
                    return $payment->student && $payment->student->current_level == $numericLevel;
                });

                if ($levelPayments->isNotEmpty()) {
                    $stats[] = [
                        'department_name' => $department->name,
                        'level' => $level,
                        'total_amount' => $levelPayments->sum('amount'),
                        'base_amount' => $levelPayments->sum('base_amount'),
                        'late_fee' => $levelPayments->sum('late_fee'),
                        'count' => $levelPayments->count()
                    ];
                }
            }

            return $stats;
        });

        // Updated Payment type statistics with additional validations
        $paymentTypeStats = PaymentType::with(['payments' => function ($query) {
            $query->where('status', '!=', 'pending');
        }])->get()->map(function ($paymentType) use ($filteredPayments) {
            // Enhanced filtering with additional validation checks
            $typePayments = $filteredPayments->filter(function ($payment) use ($paymentType) {
                return $payment->payment_type_id === $paymentType->id
                    && $payment->status !== 'pending'
                    && !empty($payment->amount)
                    && !empty($payment->student_id)
                    && !empty($payment->academic_session_id)
                    && !empty($payment->payment_date)
                    && $payment->student !== null
                    && $payment->academicSession !== null;
            });

            return [
                'name' => $paymentType->name,
                'total_amount' => $typePayments->sum('amount'),
                'base_amount' => $typePayments->sum('base_amount'),
                'late_fee' => $typePayments->sum('late_fee'),
                'count' => $typePayments->count()
            ];
        })->filter(function ($stat) {
            return $stat['count'] > 0;
        })->values();

        // Paginated payments for table display
        $payments = $query->latest()->get();

        // Prepare additional data for filtering
        $departments = Department::all();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $paymentTypes = PaymentType::all();
        $levels = [];

        if ($request->filled('department')) {
            $department = Department::find($request->department);
            $levels = $department ? $department->levels : [];
        }

        return view('admin.payments.list_of_paid', compact(
            'payments',
            'totalStats',
            'departmentStats',
            'paymentTypeStats',
            'departments',
            'academicSessions',
            'semesters',
            'levels',
            'paymentTypes'
        ));
    }

    public function exportProcessedPayments(Request $request)
    {
        // Reuse the same filtering logic from ProcessedPayments method
        $query = Payment::where('status', '!=', 'pending')
            ->with([
                'student.user',
                'student.department',
                'paymentType',
                'academicSession',
                'semester'
            ]);

        // Apply the same filters as in ProcessedPayments
        if ($request->filled('department')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('current_level', $request->level);
            });
        }

        if ($request->filled('academic_session')) {
            $query->where('academic_session_id', $request->academic_session);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type_id', $request->payment_type);
        }

        // Fetch payments with full details
        $payments = $query->get();

        // Generate filename with timestamp
        $filename = 'processed_payments_' . now()->format('YmdHis') . '.csv';

        // Return CSV download
        return response()->streamDownload(function () use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Payment ID',
                'Student Name',
                'Department',
                'Level',
                'Payment Type',
                'Academic Session',
                'Semester',
                'Fee Amount',
                'Paid Amount',
                'Late Fee',
                'Status',
                'Payment Date'
            ]);

            // Populate CSV rows
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->student?->user->full_name ?? 'N/A',
                    $payment?->student?->department->name ?? 'N/A',
                    $payment?->student->current_level ?? 'N/A',
                    $payment->paymentType->name ?? 'N/A',
                    $payment?->academicSession->name ?? 'N/A',
                    $payment->semester->name ?? 'N/A',
                    $payment->amount,
                    $payment->base_amount,
                    $payment->late_fee,
                    $payment->status,
                    $payment->updated_at
                ]);
            }

            fclose($file);
        }, $filename);
    }

    public function printProcessedPayments(Request $request)
    {
        // Reuse the same logic as ProcessedPayments method
        $query = Payment::where('status', '!=', 'pending')
            ->with([
                'student.user',
                'student.department',
                'paymentType',
                'academicSession',
                'semester'
            ]);

        // Apply the same filters as in ProcessedPayments method
        // ... (same filtering logic as in ProcessedPayments method)

        $payments = $query->latest()->get();

        // Prepare the same stats and additional data
        $totalStats = [
            'total_amount' => $payments->sum('amount'),
            'total_base_amount' => $payments->sum('base_amount'),
            'total_late_fee' => $payments->sum('late_fee'),
            'payments_count' => $payments->count()
        ];

        return view('admin.payments.print_list_of_paid', compact(
            'payments',
            'totalStats'
        ));
    }

    public function getDepartmentLevels(Department $department)
    {
        return response()->json($department->levels);
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

}
