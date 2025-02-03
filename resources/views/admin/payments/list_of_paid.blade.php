@extends('admin.layouts.admin')

@section('title', 'Success Payments')

@section('admin')
    @include('admin.alert')
    <!-- Analytics Section -->
    <div class="container-fluid mb-4">
        <!-- Summary Cards Row -->
        <div class="row">
            {{-- Total Amount Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($totalStats['total_amount'], 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Base Amount Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Base Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($totalStats['total_base_amount'], 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Late Fees Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Late Fees</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($totalStats['total_late_fee'], 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Count Card --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-muted">
                                    {{ number_format($totalStats['payments_count']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Analysis Row -->
        {{-- Detailed Statistics Tables --}}
        <div class="row">
            {{-- Department Statistics --}}
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Department Payment Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Level</th>
                                        <th>Total Amount</th>
                                        <th>Base Amount</th>
                                        <th>Late Fees</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departmentStats as $stat)
                                        <tr
                                            @if ($stat['level'] === 'All Levels') class="table-secondary font-weight-bold" @endif>
                                            <td>{{ $stat['department_name'] }}</td>
                                            <td>{{ $stat['level'] }}</td>
                                            <td>₦{{ number_format($stat['total_amount'], 2) }}</td>
                                            <td>₦{{ number_format($stat['base_amount'], 2) }}</td>
                                            <td>₦{{ number_format($stat['late_fee'], 2) }}</td>
                                            <td>{{ number_format($stat['count']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Type Statistics --}}
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Types Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Payment Type</th>
                                        <th>Total Amount</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentTypeStats as $stat)
                                        <tr>
                                            <td>{{ $stat['name'] }}</td>
                                            <td>₦{{ number_format($stat['total_amount'], 2) }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="container-fluid">
            <div class="card">

                <div class="card-header">
                    <a href="{{ route('processed.payments.export', request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-file-export"></i> Export to CSV
                    </a>
                    <a href="{{ route('processed.payments.print', request()->query()) }}" class="btn btn-info">
                        <i class="fas fa-print"></i> Print
                    </a>
                    <div class="float-end">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#filterSection">
                            <i class="bi bi-funnel"></i> Filters
                        </button>
                    </div> <br>

                </div>

                <div class="collapse show" id="filterSection">
                    <div class="card-body border-bottom">
                        <form action="{{ route('admin.payments.ProcessedPayments') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Department</label>
                                <select name="department" id="department" class="form-select">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ request('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Level</label>
                                <select name="level" id="level" class="form-select">
                                    <option value="">Select Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level }}"
                                            {{ request('level') == $level ? 'selected' : '' }}>
                                            {{ ((($level == 100
                                                            ? 'ND1/RN1'
                                                            : $level == 200)
                                                        ? 'ND2/RN2'
                                                        : $level == 300)
                                                    ? 'HND1/RN3'
                                                    : $level == 400)
                                                ? 'HND2'
                                                : $level }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Academic Session</label>
                                <select name="academic_session" class="form-select">
                                    <option value="">Select Session</option>
                                    @foreach ($academicSessions as $session)
                                        <option value="{{ $session->id }}"
                                            {{ request('academic_session') == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Payment Type</label>
                                <select name="payment_type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach ($paymentTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ request('payment_type') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="{{ route('admin.payments.ProcessedPayments') }}"
                                    class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="paymentsTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Student Details</th>
                                    <th>Payment Type</th>
                                    <th>Session/Semester</th>
                                    <th>Amount Details</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $payment->student?->user?->full_name }}<br>
                                            <small class="text-muted">{{ $payment->student?->matric_number }}</small><br>
                                            <small class="text-muted">
                                                {{ $payment->student->department->name }}
                                                (Level
                                                {{ $payment->student->department->getDisplayLevel($payment->student->current_level) }})
                                            </small>
                                        </td>
                                        <td>{{ $payment->paymentType->name }}</td>
                                        <td>
                                            {{ $payment->academicSession->name }}<br>
                                            {{ $payment->semester->name }}
                                        </td>
                                        <td>
                                            <span class="text-success">₦{{ number_format($payment->amount) }}</span><br>
                                            <small class="text-muted">Base:
                                                ₦{{ number_format($payment->base_amount) }}</small><br>
                                            <small class="text-danger">Late Fee:
                                                ₦{{ number_format($payment->late_fee) }}</small>
                                        </td>
                                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('admin.payments.ProcessedPayment_details', $payment) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                    </div>
                </div>
            </div>
        </div>

        <script>
            // JavaScript for dynamic level population
            document.addEventListener('DOMContentLoaded', function() {
                const departmentSelect = document.querySelector('#department');
                const levelSelect = document.querySelector('#level');

                departmentSelect.addEventListener('change', function() {
                    const departmentId = this.value;
                    levelSelect.innerHTML = '<option value="">Select Level</option>';

                    if (departmentId) {
                        fetch(`/admin/departments/${departmentId}/levels`)
                            .then(response => response.json())
                            .then(levels => {
                                levels.forEach(level => {
                                    const option = document.createElement('option');
                                    const numericLevelMap = {
                                        'ND1': 100,
                                        'ND2': 200,
                                        'HND1': 300,
                                        'HND2': 400,
                                        'RN1': 100,
                                        'RN2': 200,
                                        'RN3': 300
                                    };

                                    option.value = numericLevelMap[level] || level;
                                    option.textContent = level;
                                    levelSelect.appendChild(option);
                                });
                            });
                    }
                });
            });
        </script>
    @endsection
