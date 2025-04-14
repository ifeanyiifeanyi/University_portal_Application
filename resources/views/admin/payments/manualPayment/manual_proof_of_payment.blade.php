@extends('admin.layouts.admin')

@section('title', 'Proof of Payments')


@section('admin')
    @include('admin.alert')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Manual Payment Records</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Payment Type</th>
                                        <th>Amount</th>
                                        <th>Processed By</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($manualProcessedPayments as $payment)
                                        <tr>
                                            <td>{{ $loop-iteration }}</td>
                                            <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ $payment->student->user->profile_image }}"
                                                        data-lightbox="{{ $payment->student->user->full_name }}">

                                                        <img src="{{ $payment->student->user->profile_image }}"
                                                            alt="Profile Photo" class="rounded-circle" width="30"
                                                            height="30">
                                                    </a>
                                                    <div class="ms-2">
                                                        {{ $payment->student->user->full_name }}
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $payment->student->matric_number }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $payment->paymentType->name }}</td>
                                            <td>₦{{ number_format($payment->base_amount, 2) }}</td>
                                            <td>
                                                @if ($payment->processedBy)
                                                    {{ $payment->processedBy->user->full_name }}
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.manual_proof_of_payment.show', $payment) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a data-lightbox="proof of payment"
                                                    href="{{ asset('storage/' . $payment->payment_proof) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-camera"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No manual payments found</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/css/lightbox.min.css" />
@endsection
@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/js/lightbox.min.js"></script>
@endsection
