@extends('admin.layouts.admin')

@section('title', 'Payment Receipts')

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
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" {{ request('academic_session') == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Semester</label>
                                <select name="semester" class="form-control">
                                    <option value="">All Semesters</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester->id }}" {{ request('semester') == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
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

                <!-- Receipts Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Receipt No.</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Session</th>
                                <th>Semester</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $receipt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>{{ $receipt->payment->student->user->full_name }}</td>
                                    <td>₦{{ number_format($receipt->amount, 2) }}</td>
                                    <td>{{ $receipt->payment->academicSession->name }}</td>
                                    <td>{{ $receipt->payment->semester->name }}</td>
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
                                    <td colspan="7" class="text-center">No receipts found</td>
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
