@extends('admin.layouts.admin')

@section('title', 'Installments Paid')

@section('admin')
    @include('admin.alert')

    <div class="card">
        <div class="card-header  text-muted d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Installment Payments Overview</h5>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Payment Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="overdue">Overdue</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date Range</label>
                                    <input type="date" class="form-control" name="date_from">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To</label>
                                    <input type="date" class="form-control" name="date_to">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Academic Session</label>
                                    <select class="form-select" name="session_id">
                                        <option value="">All Sessions</option>
                                        @foreach ($academicSessions as $session)
                                            <option {{ $session->is_current ? 'selected' : '' }} value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Semester</label>
                                    <select class="form-select" name="semester_id">
                                        <option value="">All Semesters</option>
                                        @foreach ($semesters as $semester)
                                            <option {{ $semester->is_current ? 'selected' : '' }} value="{{ $semester->id }}">{{ $semester->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Type</label>
                                    <select class="form-select" name="payment_type">
                                        <option value="">All Payment Types</option>
                                        @foreach ($paymentTypes as $paymentType)
                                            <option value="{{ $paymentType->id }}">{{ $paymentType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="">All Payment Methods</option>
                                        @foreach ($paymentMethods as $method)
                                            <option selected value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                    <a href="{{ route('admin.installment_paid.index') }}" class="btn btn-info ms-2"> <i class="fas fa-sync-alt"></i> Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installments Table -->
            <div class="table-responsive">
                <table class="table table-striped" id="example">
                    <thead class="bg-light">
                        <tr>
                            <th>Student Info</th>
                            <th>Payment Details</th>
                            <th>Installment Info</th>
                            <th>Payment Status</th>
                            <th>Amount Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installments as $installment)
                            <tr>
                                <!-- Student Information -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $installment->payment->student->user->full_name ?? 'N/A' }}</strong>
                                        <small
                                            class="text-muted">{{ $installment->payment->student->matric_number ?? 'N/A' }}</small>
                                        <small
                                            class="text-muted">{{ $installment->payment->student->department->name ?? 'N/A' }}</small>
                                    </div>
                                </td>

                                <!-- Payment Details -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><strong>Type:</strong>
                                            {{ $installment->payment->paymentType->name ?? 'N/A' }}</span>
                                        <span><strong>Method:</strong>
                                            {{ $installment->payment->paymentMethod->name ?? 'N/A' }}</span>
                                        <small class="text-muted">Ref:
                                            {{ $installment->payment->transaction_reference }}</small>
                                    </div>
                                </td>

                                <!-- Installment Information -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><strong>Installment:</strong> {{ $installment->installment_number }} of
                                            {{ $installment->payment->installments->count() }}</span>
                                        <span><strong>Due Date:</strong>
                                            {{ $installment->due_date->format('d M, Y') }}</span>
                                        @if ($installment->paid_at)
                                            <small class="text-success">Paid on:
                                                {{ $installment->paid_at->format('d M, Y') }}</small>
                                        @endif
                                    </div>
                                </td>

                                <!-- Payment Status -->
                                <td>
                                    @php
                                        $statusClass = match ($installment->status) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'overdue' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($installment->status) }}
                                    </span>
                                </td>

                                <!-- Amount Details -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><strong>Amount:</strong> ₦{{ number_format($installment->amount, 2) }}</span>
                                        @if ($installment->paid_amount > 0)
                                            <span class="text-success"><strong>Paid:</strong>
                                                ₦{{ number_format($installment->paid_amount, 2) }}</span>
                                        @endif
                                        @if ($installment->status === 'pending' || $installment->status === 'overdue')
                                            <span class="text-danger"><strong>Due:</strong>
                                                ₦{{ number_format($installment->amount - ($installment->paid_amount ?? 0), 2) }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#detailsModal{{ $installment->id }}">
                                            View Details
                                        </button>
                                        @if ($installment->payment->receipt)
                                            <a href="{{ route('admin.payments.showReceipt', $installment->payment->receipt->id) }}"
                                                class="btn btn-sm btn-outline-success">
                                                View Receipt
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Details Modal -->
                            <div class="modal fade" id="detailsModal{{ $installment->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Installment Payment Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Payment Information</h6>
                                                    <hr>
                                                    <p><strong>Total Amount:</strong>
                                                        ₦{{ number_format($installment->payment->amount, 2) }}</p>
                                                    <p><strong>Remaining Amount:</strong>
                                                        ₦{{ number_format($installment->payment->remaining_amount ?? 0, 2) }}
                                                    </p>
                                                    <p><strong>Payment Method:</strong>
                                                        {{ $installment->payment->paymentMethod->name }}</p>
                                                        <p><strong>Payment For:</strong>
                                                            {{ Str::ucfirst($installment->payment->paymentType->name) }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Installment Details</h6>
                                                    <hr>
                                                    <p><strong>Status:</strong> {{ ucfirst($installment->status) }}</p>
                                                    <p><strong>Due Date:</strong>
                                                        {{ $installment->due_date->format('d M, Y') }}</p>
                                                    @if ($installment->paid_at)
                                                        <p><strong>Paid Date:</strong>
                                                            {{ $installment->paid_at->format('d M, Y') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No installment payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any JavaScript functionality here
        });
    </script>
@endsection
