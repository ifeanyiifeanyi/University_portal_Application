@extends('admin.layouts.print')

@section('title', 'Processed Payments Report')

@section('content')
    <div class="container-fluid">
        <!-- Summary Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="text-center mb-3">Processed Payments Report</h4>

                <div class="row card-body">
                    <div class="col-3 card">
                        <strong>Total Amount:</strong>
                        ₦{{ number_format($totalStats['total_amount'], 2) }}
                    </div>
                    <div class="col-3 card">
                        <strong>Base Amount:</strong>
                        ₦{{ number_format($totalStats['total_base_amount'], 2) }}
                    </div>
                    <div class="col-3 card">
                        <strong>Total Late Fees:</strong>
                        ₦{{ number_format($totalStats['total_late_fee'], 2) }}
                    </div>
                    <div class="col-3 card">
                        <strong>Total Payments:</strong>
                        {{ number_format($totalStats['payments_count']) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="row card py-2 px-2">
            <div class="col-12 table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student</th>
                            <th>Department</th>
                            <th>Payment Type</th>
                            <th>Session/Semester</th>
                            <th>Fee Amount</th>
                            <th>Paid Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $payment->student?->user?->first_name }} {{ $payment->student?->user?->last_name }}<br>
                                    <small>{{ $payment->student?->matric_number }}</small>
                                    <small>
                                        (Level {{ $payment->student->department->getDisplayLevel($payment->student->current_level) }})
                                    </small>
                                </td>
                                <td>
                                    {{ $payment->student->department->name }}
                                </td>
                                <td>{{ $payment->paymentType->name }}</td>
                                <td>
                                   <small> {{ $payment->academicSession->name }}<br>
                                    {{ $payment->semester->name }} </small>
                                </td>
                                <td>
                                   ₦{{ number_format($payment->amount) }}
                                </td>
                                <td>
                                    ₦{{ number_format($payment->base_amount) }}
                                </td>
                                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.print();
    </script>
@endsection
