<?php

namespace App\Http\Controllers\Student;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use App\Models\PaymentType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Services\AuthService;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DepartmentPaymentType;
use App\Services\StudentPaymentGatewayService;
use App\Models\PaymentInstallment;
use App\Models\PaymentInstallmentConfig;
use App\Http\Requests\SubmitPaymentFormRequest;
use App\Http\Requests\ProcessPaymentRequest;

class StudentFeesController extends Controller
{
    protected $authService;
    protected $StudentpaymentGatewayService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService,StudentPaymentGatewayService $StudentpaymentGatewayService){

        $this->authService = $authService;
        $this->StudentpaymentGatewayService = $StudentpaymentGatewayService;
    }
    public function index(){
        $student = Student::where('user_id',$this->authService->user()->id)->first();

    // Fetch the student's invoices, grouping by academic session and semester
    $invoices = Invoice::where('student_id', $student->id)
        ->with(['academicSession', 'semester', 'paymentType', 'department', 'paymentMethod']) // Load relationships
        ->orderBy('created_at') // Optional: order by academic session
        ->get();

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
                ->where('payments.student_id', $student->id)
                ->where('payments.status', 'completed');
        })
        ->get();

        return view('student.fees.index',compact('invoices','paymentTypes'));
    }
    public function view(){
        return view('student.fees.view');
    }

    public function getPaymentDetails(Request $request)
{
    $paymentType = PaymentType::findOrFail($request->payment_type_id);
    
    $currentDate = now();
    $lateFee = $paymentType->calculateLateFee($currentDate);
    
    // Check if student's department matches the payment type
    $departmentPaymentType = $paymentType->departments()
        ->where('department_id', $request->department_id)
        ->first();
    
    if (!$departmentPaymentType) {
        return response()->json([
            'error' => 'This payment type is not available for your department'
        ], 422);
    }
    
    return response()->json([
        'amount' => $paymentType->amount + $lateFee,
        'late_fee' => $lateFee,
        'due_date' => $paymentType->due_date,
        'supports_installments' => $paymentType->supportsInstallments(),
        'installment_config' => $paymentType->supportsInstallments() ? $paymentType->installmentConfig : null
    ]);
}

    public function pay(){
         // load the semester
         $semesters = Semester::get();
         // load the academic sessions
         $academicsessions = AcademicSession::all();
         // load the studentprofile
         $student = Student::where('user_id',$this->authService->user()->id)->first();
         $currentDepartment = Department::find($student->department_id);
         $levels = $currentDepartment ? $currentDepartment->levels : [];

  
         

    // $paymentType = PaymentType::where('slug', 'school-fees-computer-science-100-level')
    // ->whereHas('departments', function ($query) use ($student) {
    //     $query->where('departments.id', $student->department_id)
    //           ->where('department_payment_type.level', $student->current_level); // Correct pivot reference
    // })
    // ->first();
    // if (!$paymentType) {
    //     return redirect()->back()->with('error', 'Payment type not found for your department/level (Contact ICT Center)');
    // }
        //  $paymentTypes = PaymentType::get();
        
        //  $paymentTypes = DepartmentPaymentType::with('paymentType')->where('department_id',$student->department_id)->where('level',$student->current_level)->get();

        $numericLevel = $student->department->getLevelNumber($student->current_level);
        $paymentTypes = DepartmentPaymentType::with(['paymentType'])
    ->where('department_id', $student->department_id)
    ->where('level',$numericLevel)
    ->whereNotExists(function ($query) use ($student) {
        $query->select('payments.id')
            ->from('payments')
            ->whereColumn('department_payment_type.payment_type_id', 'payments.payment_type_id')
            ->where('payments.student_id', $student->id);
    })
    ->get();

    // $paymentTypes = PaymentType::select('payment_types.*')
    //         ->distinct()
    //         ->with(['departments' => function ($query) {
    //             $query->select('departments.id', 'departments.name')
    //                 ->distinct();
    //         }])
    //         ->active()
    //         ->get();

    // $numericLevel = $student->department->getLevelNumber($student->current_level);

    // $paymentTypes = DepartmentPaymentType::with(['paymentType' => function($query) {
    //         $query->where('is_active', true);
    //     }])
    //     ->where('department_id', $student->department_id)
    //     ->where('level', $numericLevel)  // Using the converted numeric level
    //     ->whereHas('paymentType', function($query) {
    //         $query->where('is_active', true);
    //     })
    //     ->whereNotExists(function ($query) use ($student) {
    //         $query->select('payments.id')
    //             ->from('payments')
    //             ->whereColumn('department_payment_type.payment_type_id', 'payments.payment_type_id')
    //             ->where('payments.student_id', $student->id)
    //             ->where('payments.status', 'completed');
    //     })
    //     ->get();
   
    
  
         $paymentMethods = PaymentMethod::where('is_active', 1)->get();
    
       return view('student.fees.pay',[
           'semesters'=>$semesters,
           'academicsessions'=>$academicsessions,
           'student'=>$student,
           'paymentMethods'=>$paymentMethods,
           'paymentTypes'=>$paymentTypes,
           'levels'=>$levels,
           'currentDepartment'=>$currentDepartment
       ]);
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

            return redirect()->route('student.view.fees.invoice', $invoice->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation or update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process invoice. Please try again.');
        }
    }

      //ths is what we see before actual payment is done
      public function invoice($invoiceId = null)
      {
          // Check if the invoiceId is missing or empty
          if (empty($invoiceId)) {
              // Redirect back to the form if no parameter is present
              return redirect()->back()->with('error', 'Invoice not found. Please try again.');
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
              return redirect()->back()->with('error', 'Invoice not found. Please try again.');
          }
  
          // Get all active payment methods
          $paymentMethods = PaymentMethod::active()->get();
  
          // Return the view with the found invoice

          return view('student.fees.invoice', compact('invoice', 'paymentMethods'));
      }
    


    public function levels(Department $department)
    {
        return response()->json($department->levels);
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

    // Determine if this is an installment payment
    $isInstallment = $request->has('is_installment') && $request->is_installment && $paymentType->supportsInstallments();

    DB::beginTransaction();

    try {
        // Check for existing payment
        $existingPayment = $this->findExistingPayment($validated);

        // Handle existing installment payments
        if ($existingPayment && $existingPayment->is_installment) {
            // If it's a pending installment payment, process next installment
            $pendingInstallment = $existingPayment->installments()
                ->where('status', 'pending')
                ->orderBy('installment_number')
                ->first();

            if ($pendingInstallment) {
                $paymentAmountForGateway = $pendingInstallment->amount;
                
                // Generate new unique transaction reference for this installment
                $newTransactionRef = 'PAY' . uniqid() . '-INST' . $pendingInstallment->installment_number;
                
                $existingPayment->update([
                    'status' => 'pending',
                    'next_transaction_amount' => $paymentAmountForGateway,
                    'transaction_reference' => $newTransactionRef // Update with new reference
                ]);

                $paymentUrl = $this->StudentpaymentGatewayService->initializePayment(
                    $existingPayment,
                    $paymentAmountForGateway
                );

                DB::commit();
                return redirect()->away($paymentUrl);
            }
        }

        // Block if payment exists and is not a pending installment
        if ($existingPayment && $existingPayment->status !== 'pending') {
            DB::rollBack();
            return redirect()->back()->withError('Payment already exists for this student.');
        }

        // For full payments, use total amount directly
        $paymentAmountForGateway = $totalAmountDue;
        $installmentConfig = null;

        // Only process installment logic if explicitly requested
        if ($isInstallment) {
            $installmentConfig = PaymentInstallmentConfig::where('payment_type_id', $paymentType->id)
                ->where('is_active', true)
                ->first();
                
            if (!$installmentConfig) {
                throw new \Exception('No active installment configuration found for this payment type');
            }

            // Calculate first installment amount only for installment payments
            $firstInstallmentPercentage = $installmentConfig->minimum_first_payment_percentage;
            $paymentAmountForGateway = ($totalAmountDue * $firstInstallmentPercentage) / 100;
        }

        // Create or update payment record with a new transaction reference
        $payment = $this->createOrUpdatePayment(
            $existingPayment,
            $validated,
            $totalAmountDue,
            $baseAmount,
            $lateFee,
            $isInstallment
        );

        // Only create installment records if installment payment is requested
        if ($isInstallment && $installmentConfig) {
            $payment->update([
                'next_transaction_amount' => $paymentAmountForGateway,
                'remaining_amount' => $totalAmountDue - $paymentAmountForGateway,
                'next_installment_date' => now()->addDays($installmentConfig->interval_days),
                'payment_installment_configs_id' => $installmentConfig->id
            ]);

            $this->createInstallmentRecords($payment, $installmentConfig, $totalAmountDue);
        }

        // Initialize payment with the gateway
        $paymentUrl = $this->StudentpaymentGatewayService->initializePayment(
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
            'admin_id' => 0,
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
        $admin = 1;

        DB::beginTransaction();

        try {
            $payment = Payment::where('transaction_reference', $reference)
                ->with(['invoice', 'student.user', 'installments'])
                ->firstOrFail();

            $result = $this->StudentpaymentGatewayService->verifyPayment($gateway, $reference);


            if ($result['success']) {
                // if ($payment->is_installment) {
                //     $this->handleInstallmentVerification($payment, $result['amount']);
                // } else {
                //     $this->handleFullPaymentVerification($payment, $admin);
                // }

                // Generate receipt and send notifications
                $receipt = $this->generateReceipt($payment);

                DB::commit();

                return redirect()->route('student.fees.payments.showReceipt', $receipt->id)
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

            return redirect()->back()
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
            'admin_comment' => "Payment was processed by student"
        ]);

        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'paid']);
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

        return view('student.fees.show-receipt', compact('receipt'));
    }


    public function processInstallmentPayment(Request $request, $paymentId)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::with(['installments', 'paymentType'])
                ->findOrFail($paymentId);
    
            if (!$payment->is_installment) {
                throw new \Exception('This is not an installment payment');
            }
    
            // Get next pending installment
            $nextInstallment = $payment->installments()
                ->where('status', 'pending')
                ->orderBy('installment_number')
                ->first();
    
            if (!$nextInstallment) {
                throw new \Exception('No pending installments found');
            }
    
            // Calculate amount including any late fees
            $installmentAmount = $nextInstallment->amount;
            if (now()->isAfter($nextInstallment->due_date)) {
                $installmentAmount += $nextInstallment->calculatePenalty();
            }
    
            // Generate new unique transaction reference
            $newReference = 'PAY-INST-' . uniqid() . '-' . $nextInstallment->installment_number;
    
            // Update payment tracking fields
            $payment->update([
                'next_transaction_amount' => $installmentAmount,
                'next_installment_date' => $nextInstallment->due_date,
                'transaction_reference' => $newReference // Update with new reference
            ]);
    
            // Initialize payment with gateway
            $paymentUrl = $this->StudentpaymentGatewayService->initializePayment(
                $payment,
                $installmentAmount
            );

            
    
            DB::commit();
            return redirect()->away($paymentUrl);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Installment payment processing failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to process installment payment: ' . $e->getMessage());
        }
    }

    public function checkPaymentStatus()
    {
        try {
            // Get current student
            $student = Student::where('user_id', $this->authService->user()->id)->firstOrFail();

            // Get current semester and session
            $semester = Semester::where('is_current', 1)->first();
            $session = AcademicSession::where('is_current', 1)->first();

            if (!$semester || !$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current semester or session not set.',
                    'shouldShowModal' => true
                ]);
            }

            // Check for unpaid installments
            $payment = Payment::where('department_id', $student->department_id)
                ->where('semester_id', $semester->id)
                ->where('academic_session_id', $session->id)
                ->where('status', '!=', 'paid')
                ->where('installment_status', '!=', 'completed')
                ->latest()
                ->first();

            if ($payment) {
                $nextInstallment = $payment->installments()
                    ->where('status', 'pending')
                    ->orderBy('due_date')
                    ->first();

                if ($nextInstallment) {
                    if ($nextInstallment->due_date->isFuture()) {
                        return response()->json([
                            'status' => 'warning',
                            'message' => 'Your next installment is due on ' . $nextInstallment->due_date->format('Y-m-d'),
                            'shouldShowModal' => true
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Our records indicate that your fees for the current session and semester have not yet been paid. Kindly proceed with the necessary payments to avoid any disruptions to your academic activities',
                    // 'shouldShowModal' => true
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => null,
                'shouldShowModal' => false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking payment status.',
                'shouldShowModal' => false
            ]);
        }
    }
}
