@extends('admin.layouts.admin')

@section('title', 'Next Installment')


@section('admin')
    @include('admin.alert')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Installment Payment Details</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Student Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Student Name:</th>
                                <td>{{ $installment->payment->student->user->fullName() }}</td>
                            </tr>
                            <tr>
                                <th>Matric Number:</th>
                                <td>{{ $installment->payment->student->matric_number }}</td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td>{{ $installment->payment->student->department->name }}</td>
                            </tr>
                            <tr>
                                <th>Academic Level:</th>
                                <td>
                                    {{ $installment->payment->student->department
                                    ->getDisplayLevel($installment->payment->student->current_level) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Payment Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Payment Type:</th>
                                <td>{{ $installment->payment->paymentType->name }}</td>
                            </tr>
                            <tr>
                                <th>Academic Session:</th>
                                <td>{{ $installment->payment->academicSession->name }}</td>
                            </tr>
                            <tr>
                                <th>Semester:</th>
                                <td>{{ $installment->payment->semester->name }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>{{ $installment->payment->paymentMethod->name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Installment Details</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Installment Number:</th>
                                        <td>{{ $installment->installment_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Base Amount:</th>
                                        <td>₦{{ number_format($installment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Late Fee:</th>
                                        <td>₦{{ number_format($lateFee, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Amount Due:</th>
                                        <td class="font-weight-bold text-primary">₦{{ number_format($totalAmount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Due Date:</th>
                                        <td
                                            class="{{ $installment->due_date->isPast() ? 'text-danger' : 'text-success' }}">
                                            {{ $installment->due_date->format('d M, Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $installment->status === 'overdue' ? 'danger' : 'warning' }}">
                                                {{ ucfirst($installment->status) }}

                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <div class="text-center mt-4">
                                    <form action="{{ route('admin.payments.installments.process', $installment) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            Process Payment (₦{{ number_format($totalAmount, 2) }})
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('css')

@endsection
@section('javascript')

@endsection
