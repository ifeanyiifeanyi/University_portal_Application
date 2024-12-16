@extends('admin.layouts.admin')

@section('title', 'Success Payments')


@section('admin')
    @include('admin.alert')
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
                                            <td>{{ number_format($payment->amount) }}</td>
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

@endsection
@section('javascript')

@endsection
