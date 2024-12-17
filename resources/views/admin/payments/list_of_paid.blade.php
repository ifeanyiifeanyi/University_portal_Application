@extends('admin.layouts.admin')

@section('title', 'Success Payments')


@section('admin')
    @include('admin.alert')
    <!-- Analytics Section -->
    <div class="container-fluid mb-4">
        <div class="row">
            <!-- Total Amount Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($payments->sum('amount')) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Payments Count -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $payments->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Late Fees -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Late Fees</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($payments->sum('late_fee')) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Payment -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Average Payment</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($payments->avg('amount'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Analysis Row -->
        <div class="row">
            <!-- Department Analysis -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Payments by Department</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Count</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $deptStats = $payments->groupBy('student.department.name');
                                    @endphp
                                    @foreach ($deptStats as $dept => $deptPayments)
                                        <tr>
                                            <td>{{ $dept }}</td>
                                            <td>{{ $deptPayments->count() }}</td>
                                            <td>₦{{ number_format($deptPayments->sum('amount')) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Timeline -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Payment Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline-small">
                            @foreach ($payments->take(5) as $payment)
                                <div class="timeline-item">
                                    <div class="row">
                                        <div class="col-auto text-center">
                                            <div class="timeline-date">
                                                {{ $payment->created_at->format('M d') }}
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="timeline-content">
                                                <strong>{{ $payment->student->user->full_name }}</strong>
                                                <p class="mb-0">
                                                    Paid ₦{{ number_format($payment->amount) }} for
                                                    {{ $payment->paymentType->name ?? 'Payment' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 float-start">Successful Payment Management</h5>

                        <div class="float-end">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                                data-bs-target="#filterSection">
                                <i class="bi bi-funnel"></i> Filters
                            </button>
                        </div>
                    </div>

                    <div class="collapse" id="filterSection">
                        <div class="card-body border-bottom">
                            <form action="{{ route('admin.payments.ProcessedPayments') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Academic Session</label>
                                    <select name="status" class="form-select">
                                        <option value="">Select Academic Session</option>
                                        @foreach ($academicSessions as $session)
                                            <option value="{{ $session->name }}"
                                                {{ $session->is_current ? 'selected' : '' }}>
                                                {{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Semester</label>
                                    <select name="priority" class="form-select">
                                        <option value="">Select semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->name }}"
                                                {{ $semester->is_current ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Department</label>
                                    <select name="department" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request('department') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Date Range</label>
                                    <select name="date_range" class="form-select">
                                        <option value="">All Time</option>
                                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>
                                            Today</option>
                                        <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>
                                            This Week</option>
                                        <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>
                                            This Month</option>
                                        <option value="year" {{ request('date_range') === 'year' ? 'selected' : '' }}>
                                            This Year</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        value="{{ request('search') }}"
                                        placeholder="Search by department name, matric number, or student name...">
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <a href="{{ route('admin.payments.ProcessedPayments') }}"
                                        class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Student Name</th>
                                        <th>Department</th>
                                        <th>Session/Semester</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>
                                                {{ $payment->student->user->full_name }} <br>
                                                <small class="text-muted">{{ $payment->student->matric_number }}</small>
                                            </td>
                                            <td>{{ $payment->student->department->name }}</td>
                                            <td>{{ $payment->academicSession->name }} <br> {{ $payment->semester->name }}
                                            </td>
                                            <td>
                                                <small class="text-success"><b>Paid:
                                                    </b>₦{{ number_format($payment->amount) }}</small> <br>
                                                <small class="text-primary"><b>Base:
                                                    </b>₦{{ number_format($payment->base_amount) }}</small> <br>
                                                <small class="text-danger"><b>Pen:
                                                    </b>₦{{ number_format($payment->late_fee) }}</small> <br>
                                            </td>
                                            <td>
                                                {{ $payment->created_at->format('M d, Y h:i A') }} <br>
                                                <small
                                                    class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.payments.ProcessedPayment_details', $payment) }}"
                                                    class="badge bg-primary">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                    view Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
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
    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }

        .timeline-small {
            padding: 20px;
        }

        .timeline-item {
            padding-bottom: 1rem;
            position: relative;
        }

        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: 14px;
            top: 24px;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-date {
            width: 30px;
            height: 30px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            color: #4e73df;
        }

        .timeline-content {
            padding-left: 1rem;
            border-left: 2px solid #e9ecef;
        }
    </style>
@endsection
@section('javascript')

@endsection
