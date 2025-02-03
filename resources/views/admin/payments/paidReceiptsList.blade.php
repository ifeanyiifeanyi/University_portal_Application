@extends('admin.layouts.admin')

@section('title', 'Payment Receipts')

@php
    function getStatusColor($status)
    {
        return match ($status) {
            'paid' => 'success',
            'partial' => 'info',
            'pending' => 'warning',
            'processing' => 'primary',
            'failed' => 'danger',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }
@endphp
@section('admin')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Receipts</h3>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.payments.paidReceipts') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Academic Session</label>
                                <select name="academic_session" class="form-control">
                                    <option value="">All Sessions</option>
                                    @foreach ($academicSessions as $session)
                                        <option value="{{ $session->id }}"
                                            {{ request('academic_session') == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Payment Status</label>
                                <select name="payment_status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>
                                        Partial</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="processing"
                                        {{ request('payment_status') == 'processing' ? 'selected' : '' }}>Processing
                                    </option>
                                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>
                                        Failed</option>
                                    <option value="rejected"
                                        {{ request('payment_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="cancelled"
                                        {{ request('payment_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded"
                                        {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Payment Type</label>
                                <select name="payment_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="installment"
                                        {{ request('payment_type') == 'installment' ? 'selected' : '' }}>Installment
                                    </option>
                                    <option value="full" {{ request('payment_type') == 'full' ? 'selected' : '' }}>Full
                                        Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.payments.paidReceipts') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Receipt No.</th>
                                <th>Student</th>
                                <th>Payment Type</th>
                                <th>Total Fee</th>
                                <th>Amount Paid</th>
                                <th>Remaining</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $receipt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $receipt?->receipt_number }}</td>
                                    <td>{{ $receipt->payment?->student?->user?->full_name }}</td>
                                    <td>
                                        {{ $receipt->payment?->is_installment ? 'Installment' : 'Full Payment' }}
                                        @if ($receipt->payment?->is_installment)
                                            <br>
                                            <small class="badge bg-info">
                                                {{ ucfirst($receipt->payment?->installment_status) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>₦{{ number_format($receipt->payment?->amount, 2) }}</div>

                                    </td>
                                    <td>
                                        <div>₦{{ number_format($receipt->payment?->base_amount, 2) }}</div>
                                        @if ($receipt->payment?->late_fee > 0)
                                            <small class="badge bg-warning">
                                                +₦{{ number_format($receipt->payment?->late_fee, 2) }} Late Fee
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($receipt->payment?->remaining_amount > 0)
                                            <span class="text-danger">
                                                ₦{{ number_format($receipt->payment?->remaining_amount, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">Fully Paid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getStatusColor($receipt->payment?->status) }}">
                                            {{ ucfirst($receipt->payment?->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $receipt->date->format('d M, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.payments.showReceipt', $receipt) }}"
                                            class="btn btn-sm btn-info">
                                            View Receipt
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No receipts found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $receipts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
