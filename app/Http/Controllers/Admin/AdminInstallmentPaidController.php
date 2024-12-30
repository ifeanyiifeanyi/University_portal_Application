<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\AcademicSession;
use App\Models\PaymentInstallment;
use App\Http\Controllers\Controller;

class AdminInstallmentPaidController extends Controller
{
    public function index()
    {
        $installments = PaymentInstallment::with([
            'payment.student',
            'payment',
            'payment.invoice',
            'payment.receipt',
            'payment.paymentType',
            'payment.paymentMethod'
        ])->get();
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $paymentMethods = PaymentMethod::all();
        $paymentTypes = PaymentType::all();

        return view('admin.payments.installments.installments', compact('installments', 'academicSessions', 'semesters', 'paymentMethods', 'paymentTypes'));
    }
}
