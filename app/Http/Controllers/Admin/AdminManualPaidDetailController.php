<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;

class AdminManualPaidDetailController extends Controller
{
    public function index()
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $manualProcessedPayments = Payment::where('is_manual', true)
            ->where('admin_id','!=', null)
            ->where('academic_session_id', $currentSession->id)

            ->where('semester_id', $currentSemester->id)
            ->with([
                'receipt',
                'student.user',
                'academicSession',
                'semester',
                'paymentType',
                'paymentMethod',
                'invoice',
                'paymentType.proveOfPayment',
                'processedBy'
            ])
            ->latest()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payments.manualPayment.manual_proof_of_payment', compact('manualProcessedPayments'));
    }

    public function show($id)
    {
        $payment = Payment::where('is_manual', true)
            ->with([
                'student',
                'paymentType',
                'paymentType.proveOfPayment',
                'processedBy',
                'academicSession',
                'semester',
                'invoice.proveOfPayment' 
            ])
            ->findOrFail($id);
        // dd($payment);

        return view('admin.payments.manualPayment.show', compact('payment'));
    }
}
