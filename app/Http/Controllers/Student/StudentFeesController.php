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
use App\Services\StudentPaymentGatewayService;

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
        return view('student.fees.index',compact('invoices'));
    }
    public function view(){
        return view('student.fees.view');
    }

    public function pay(){
         // load the semester
         $semesters = Semester::get();
         // load the academic sessions
         $academicsessions = AcademicSession::all();
         // load the studentprofile
         $student = Student::where('user_id',$this->authService->user()->id)->first();

  
         

    // $paymentType = PaymentType::where('slug', 'school-fees-computer-science-100-level')
    // ->whereHas('departments', function ($query) use ($student) {
    //     $query->where('departments.id', $student->department_id)
    //           ->where('department_payment_type.level', $student->current_level); // Correct pivot reference
    // })
    // ->first();
    // if (!$paymentType) {
    //     return redirect()->back()->with('error', 'Payment type not found for your department/level (Contact ICT Center)');
    // }
         $paymentTypes = PaymentType::get();
         $paymentMethods = PaymentMethod::where('is_active', 1)->get();
        
       return view('student.fees.pay',[
           'semesters'=>$semesters,
           'academicsessions'=>$academicsessions,
           'student'=>$student,
           'paymentMethods'=>$paymentMethods,
           'paymentTypes'=>$paymentTypes
       ]);
    }

    public function process(Request $request){
        $request->validate([
            'session'=>'required|integer',
            'level'=>'required|integer',
            'semester'=>'required|integer',
            'payment_method'=>'required|integer'
        ]);
        $student = Student::where('user_id',$this->authService->user()->id)->first();

        // Get the payment type details
    $paymentType = PaymentType::find($request->payment_type_id);

    // Generate invoice number (you can generate this however you'd like, e.g., unique string)
    $invoiceNumber = 'INV-' . strtoupper(Str::random(10));

    // get the level the student should pay the school fees
    $departmenttypes = DB::table('department_payment_type')->where('department_id',$student->department_id)->where('payment_type_id',$paymentType->id)->first();

    // check if user have paid for the fees already from the payments table
    // use studentid,departmentid,sessionid,semesterid,level
    $checkpayment = Payment::where('department_id',$student->department_id)->where('student_id',$student->id)->where('academic_session_id',$paymentType->academic_session_id)->where('semester_id',$paymentType->semester_id)->where('level',$departmenttypes->level)->where('status','paid')->first();
     if ($checkpayment) {
        return redirect()->route('student.view.fees.pay')->with('error', 'You have paid for this school fees already(Check your receipt page and print)');
    }
    // check in case of duplicate invoice
    $checkinvoice = Invoice::where('department_id',$student->department_id)->where('student_id',$student->id)->where('academic_session_id',$paymentType->academic_session_id)->where('semester_id',$paymentType->semester_id)->where('level',$departmenttypes->level)->first();
     if ($checkinvoice) {
        return redirect()->route('student.view.fees.pay')->with('error', 'You have generated this invoice already(Check invoice history)');
    }
    // Create the invoice
    $invoice = Invoice::create([
        'student_id' => $student->id,
        'payment_type_id' => $request->payment_type_id,

        // to adjust
        'department_id' => $student->department_id,
        'level' => $departmenttypes->level,
        'academic_session_id' => $paymentType->academic_session_id,
        // to adjust


        'semester_id' => $paymentType->semester_id,
        'amount' => $paymentType->amount, // amount from the payment_types table
        'payment_method_id' => $request->payment_method,
        'status' => 'pending',
        'invoice_number' => $invoiceNumber,
    ]);

    return redirect()->route('student.view.fees.invoice',['id'=>$invoice->id]);
        
    }

    public function invoice($invoiceId = null){
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

        // // Check if the invoice was not found
        if (!($invoice)) {
            // Redirect back to the form if no invoice was found
            return redirect()->back()->with('error', 'Invoice not found. Please try again.');
        }

        return view('student.fees.invoice', compact('invoice'));
        
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
        ]);

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'student_id' => $validated['student_id'],
                'payment_type_id' => $validated['payment_type_id'],
                'payment_method_id' => $validated['payment_method_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'semester_id' => $validated['semester_id'],
                'amount' => $request->amount,
                'department_id' => $validated['department_id'],
                'level' => $validated['level'],
                'status' => 'pending',
                'transaction_reference' => 'PAY-' . uniqid(),
                'payment_date' => now()
            ]);
            Log::info(route('student.fees.payment.verify', ['gateway' => 'paystack']));
            $paymentUrl = $this->StudentpaymentGatewayService->initializePayment($payment);
            // dd($paymentUrl);

            DB::commit();

            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initialization failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to initialize payment. Please try again later.');
        }
    }

    public function verifyPayment(Request $request, $gateway)
    {
        $reference = $request->query('reference');

        DB::beginTransaction();

        try {
            $result = $this->StudentpaymentGatewayService->verifyPayment($gateway, $reference);

            if ($result['success']) {
                $payment = Payment::where('transaction_reference', $reference)->firstOrFail();
                $payment->status = 'paid';
                $payment->save();

                $receipt = $this->generateReceipt($payment);

                DB::commit();

                return redirect()->route('student.fees.payments.showReceipt', $receipt->id)
                    ->with('success', 'Payment verified successfully')
                    ->with('receipt', $receipt);
            } else {
                throw new \Exception('Payment verification failed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->route('student.view.fees.invoice')
                ->with('error', 'Payment verification failed. Please contact support if you believe this is an error.');
        }
    }

    protected function generateReceipt(Payment $payment)
    {
        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => 'REC-' . uniqid(),
            'amount' => $payment->amount,
            'date' => now(),
        ]);
    }

    public function showReceipt(Receipt $receipt)
    {
         return view('student.fees.show-receipt', compact('receipt'));
    }

    public function levels(Department $department)
    {
        return response()->json($department->levels);
    }
}
