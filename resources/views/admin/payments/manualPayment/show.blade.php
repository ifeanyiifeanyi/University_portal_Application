@extends('admin.layouts.admin')

@section('title', 'Payment Details')

@section('admin')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Details</h3>
                        <a href="{{ route('admin.manual_proof_of_payment.index') }}"
                           class="btn btn-secondary float-right">
                            Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 table-responsive">
                                <h5>Student Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $payment->student->user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Matric Number</th>
                                        <td>{{ $payment->student->matric_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ $payment->student->department->name }}</td>
                                    </tr>
                                </table>
                                <hr>
                                <h5>Payment Manager</h5>
                                <table class="table table-bordered">
                                    @if($payment->processedBy)
                                        <tr>
                                            <th>Payment Manager</th>
                                            <td>{{ $payment->processedBy->user->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                {{ $payment->admin_comment ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th>Payment Manager</th>
                                            <td class="text-muted">Not specified</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Payment Type</th>
                                        <td>{{ $payment->paymentType->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Channel</th>
                                        <td>{{  $payment->payment_channel }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount Paid</th>
                                        <td>₦{{ number_format($payment->base_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Late Fee</th>
                                        <td>₦{{ number_format($payment->late_fee, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Expected Total Amount</th>
                                        <td>₦{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date</th>
                                        <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Academic Session</th>
                                        <td>{{ $payment->academicSession->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Semester</th>
                                        <td>{{ $payment->semester->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($payment->is_installment)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Installment Details</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Installment #</th>
                                                    <th>Amount</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Paid Date</th>
                                                    <th>Payment Reference</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payment->installments as $installment)
                                                    <tr>
                                                        <td>{{ $installment->installment_number }}</td>
                                                        <td>₦{{ number_format($installment->amount, 2) }}</td>
                                                        <td>{{ $installment->due_date->format('d M, Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $installment->status === 'paid' ? 'success' : ($installment->status === 'pending' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($installment->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ $installment->paid_at ? $installment->paid_at->format('d M, Y') : 'Not paid' }}
                                                        </td>
                                                        <td>{{ $installment->payment->transaction_reference ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th colspan="1">Summary</th>
                                                    <td colspan="5">
                                                        <strong>Total Amount:</strong> ₦{{ number_format($payment->amount, 2) }} |
                                                        <strong>Paid So Far:</strong> ₦{{ number_format($payment->installments->where('status', 'paid')->sum('amount'), 2) }} |
                                                        <strong>Remaining:</strong> ₦{{ number_format($payment->remaining_amount, 2) }}
                                                        @if($payment->next_installment_date)
                                                            <br>
                                                            <strong>Next Payment Due:</strong> {{ $payment->next_installment_date->format('d M, Y') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="1">Additional Note</th>
                                                    <td colspan="5">{{ $payment->installments->first()->additional_notes }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
