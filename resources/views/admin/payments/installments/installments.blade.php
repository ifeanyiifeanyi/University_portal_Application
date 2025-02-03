@extends('admin.layouts.admin')

@section('title', 'Installments Paid')

@section('admin')
    @include('admin.alert')

    <div class="card">
        <div class="card-header text-muted d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Installment Payments Overview</h5>
            <div>
                <a href="{{ route('admin.installment_paid.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Export Data
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Enhanced Filters Section -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Payment Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>
                                            Overdue</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" class="form-control" name="date_from"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" class="form-control" name="date_to"
                                        value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Academic Session</label>
                                    <select class="form-select" name="session_id">
                                        <option value="">All Sessions</option>
                                        @foreach ($academicSessions as $session)
                                            <option value="{{ $session->id }}"
                                                {{ request('session_id') == $session->id || ($session->is_current && !request('session_id')) ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Semester</label>
                                    <select class="form-select" name="semester_id">
                                        <option value="">All Semesters</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ request('semester_id') == $semester->id || ($semester->is_current && !request('semester_id')) ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Type</label>
                                    <select class="form-select" name="payment_type">
                                        <option value="">All Payment Types</option>
                                        @foreach ($paymentTypes as $paymentType)
                                            <option value="{{ $paymentType->id }}"
                                                {{ request('payment_type') == $paymentType->id ? 'selected' : '' }}>
                                                {{ $paymentType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="">All Payment Methods</option>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method->id }}"
                                                {{ request('payment_method') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.installment_paid.index') }}" class="btn btn-info ms-2">
                                        <i class="fas fa-sync-alt me-1"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Installments</h6>
                            <h3 class="mb-0">{{ $installments->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Paid Installments</h6>
                            <h3 class="mb-0">{{ $installments->where('status', 'paid')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h6 class="card-title">Pending Installments</h6>
                            <h3 class="mb-0">{{ $installments->where('status', 'pending')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6 class="card-title">Overdue Installments</h6>
                            <h3 class="mb-0">{{ $installments->where('status', 'overdue')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installments Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" ids="installmentsTable" id="example">
                    <thead class="bg-light">
                        <tr>
                            <th>Student Info</th>
                            <th>Payment Details</th>
                            <th>Installment Info</th>
                            <th>Payment Status</th>
                            <th>Amount Details</th>
                            <th>Created At</th>
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
                                            {{ $installment->payment->transaction_reference ?? 'N/A' }}</small>
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

                                <!-- Payment Status with Pay Button -->
                                <td>
                                    @php
                                        $statusClass = match ($installment->status) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'overdue' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <div class="d-flex flex-column gap-2">
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($installment->status) }}
                                        </span>
                                        @if (
                                            ($installment->status === 'pending' || $installment->status === 'overdue') &&
                                                $installment->payment->installment_status !== 'completed')
                                            <a href="{{ route('admin.payments.installments.details', $installment) }}"
                                                class="btn btn-primary btn-sm">
                                                Process Next Payment
                                            </a>
                                        @elseif ($installment->payment->installment_status === 'completed')
                                            <small class="text-success">All Installments Completed</small>
                                        @endif
                                    </div>
                                </td>

                                <!-- Amount Details -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><strong>Amount:</strong> ₦{{ number_format($installment->amount, 2) }}</span>
                                        @if ($installment->paid_amount > 0)
                                            <span class="text-success">
                                                <strong>Paid:</strong> ₦{{ number_format($installment->paid_amount, 2) }}
                                            </span>
                                        @endif
                                        @if ($installment->status === 'pending' || $installment->status === 'overdue')
                                            <span class="text-danger">
                                                <strong>Due:</strong>
                                                ₦{{ number_format($installment->amount - ($installment->paid_amount ?? 0), 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <p class="text-muted">{{ $installment->created_at?->format('Y-m-d') }}</p>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" data-bs-target="#detailsModal{{ $installment->id }}">
                                            <i class="fas fa-eye me-1"></i> Details
                                        </button>
                                        @if (optional($installment->payment)->receipt)
                                        <a href="{{ route('admin.payments.showReceipt', $installment->payment->receipt->id) }}"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-file-alt me-1"></i> Receipt
                                        </a>
                                        @endif
                                    </div>

                                </td>
                            </tr>

                            <!-- Details Modal -->
                            @include('admin.payments.installments.modal', ['installment' => $installment])
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No installment payments found.</td>
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
            padding: 0.75rem;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.5em 0.75em;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .card-body {
            padding: 1.25rem;
        }

        .table-responsive {
            margin-bottom: 1rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }
    </style>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTables
            $('#installmentsTable').DataTable({
                pageLength: 25,
                ordering: true,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endsection
