@extends('admin.layouts.admin')

@section('title', 'Payment Details')

@section('admin')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment Details</h5>
                        <div class="float-end">
                            <a href="{{ route('admin.payments.ProcessedPayments') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Student Information -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2">Student Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Name:</th>
                                        <td>{{ $payment->student->user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Matric Number:</th>
                                        <td>{{ $payment->student->matric_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department:</th>
                                        <td>{{ $payment->student->department->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Level:</th>
                                        <td>{{ $payment->level }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Payment Information -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2">Payment Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Payment Type:</th>
                                        <td>{{ $payment->paymentType->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td>₦{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Session:</th>
                                        <td>{{ $payment->academicSession->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Semester:</th>
                                        <td>{{ $payment->semester->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method:</th>
                                        <td>{{ $payment->paymentMethod->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Ref:</th>
                                        <td>{{ $payment->transaction_reference }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date:</th>
                                        <td>{{ $payment->payment_date->format('M d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Invoice & Receipt Details -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Invoice Details</h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($payment->invoice)
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="150">Invoice Number:</th>
                                                    <td>{{ $payment->invoice->invoice_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status:</th>
                                                    <td><span class="badge bg-success">Paid</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Generated Date:</th>
                                                    <td>{{ $payment->invoice->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                            
                                        @else
                                            <p class="text-muted">No invoice information available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Receipt Details</h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($payment->receipt)
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="150">Receipt Number:</th>
                                                    <td>{{ $payment->receipt->receipt_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Amount:</th>
                                                    <td>₦{{ number_format($payment->receipt->amount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date:</th>
                                                    <td>{{ $payment->receipt->date->format('M d, Y') }}</td>
                                                </tr>
                                            </table>
                                          
                                        @else
                                            <p class="text-muted">No receipt information available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
