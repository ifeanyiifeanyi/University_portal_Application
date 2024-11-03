{{-- resources/views/admin/invoices/show.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Invoice Detail')

@section('admin')
    @include('admin.alert')
    <div class="container">
        <div class="row">
            {{-- @dd($invoice) --}}
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="card-title">Invoice Details</h4>
                        <div class="float-end">
                            <a href="" class="btn btn-secondary">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                            @if($invoice->status === 'pending')
                                <a href="" class="btn btn-success"
                                    onclick="return confirm('Are you sure you want to mark this invoice as paid?')">
                                    <i class="fas fa-check"></i> Mark as Paid
                                </a>
                                <a href="" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to cancel this invoice?')">
                                    <i class="fas fa-times"></i> Cancel Invoice
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Invoice Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Invoice Number</th>
                                        <td>{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>₦{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Type</th>
                                        <td>{{ $invoice->paymentType->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td>{{ $invoice->paymentMethod->name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Student Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Student Name</th>
                                        <td>{{ $invoice->student->user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ $invoice->department->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Level</th>
                                        <td>{{ $invoice->level }}</td>
                                    </tr>
                                    <tr>
                                        <th>Academic Session</th>
                                        <td>{{ $invoice->academicSession->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Semester</th>
                                        <td>{{ $invoice->semester->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($invoice->payment)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Payment Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Payment Date</th>
                                            <td>{{ $invoice->payment->created_at->format('d M, Y H:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Transaction Reference</th>
                                            <td>{{ $invoice->payment->transaction_reference }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
