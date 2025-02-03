@extends('admin.layouts.admin')

@section('title', 'Unpaid Fees')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
@endsection

@section('admin')
    <div class="container-fluid">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 fw-bold">Students With Unpaid Fees</h1>
                <div>
                    <button onclick="printTable()" class="btn btn-secondary me-2">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="{{ route('admin.unpaid-fees.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <form action="" method="GET" class="bg-white p-4 rounded shadow-sm mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="department" class="form-label">Department</label>
                        <select name="department" id="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(request('department') == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="level" class="form-label">Level</label>
                        <select name="level" id="level" class="form-select">
                            <option value="">All Levels</option>
                            @foreach ($departments->first()->levels ?? [] as $level)
                                <option value="{{ $level }}" @selected(request('level') == $level)>
                                    {{ $level }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="session" class="form-label">Session</label>
                        <select name="session" id="session" class="form-select">
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}" @selected($session->id == $currentSession->id)>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select name="semester" id="semester" class="form-select">
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected($semester->id == $currentSemester->id)>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-sm">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Defaulters</h5>
                            <p class="display-6 fw-bold text-primary">{{ $statistics['total_defaulters'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Amount Owed</h5>
                            <p class="display-6 fw-bold text-danger">
                                ₦{{ number_format($statistics['total_amount_owed'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Current Session</h5>
                            <p class="display-6 fw-bold text-purple">{{ $currentSession->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Defaulters by Department</h5>
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Defaulters by Level</h5>
                            <canvas id="levelChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <div class="table-responsive" id="printableTable">
                        <table class="table table-hover" id="example">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th scope="col">Student</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Amount Owed</th>
                                    <th scope="col">Payment Types Due</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <img src="{{ $student->user->profile_image }}"
                                                        alt="{{ $student->user->full_name }}" class="rounded-circle"
                                                        width="40" height="40">
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $student->user->full_name }}</div>
                                                    <div class="text-muted">{{ $student->matric_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ Str::title($student->department->name) }}</td>
                                        <td>{{ $student->current_level }}</td>
                                        <td class="text-danger fw-bold">₦{{ number_format($student->debtDetails['total'], 2) }}</td>
                                        <td>
                                            <div class="small">
                                                @foreach($student->debtDetails['breakdown'] as $payment)
                                                    <div class="mb-1">
                                                        <span class="fw-bold">{{ $payment['name'] }}</span>
                                                        <span class="text-muted">- ₦{{ number_format($payment['amount'], 2) }}</span>
                                                        @if($payment['due_date'])
                                                            <br>
                                                            <small class="text-danger">Due: {{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</small>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.student.details', $student) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No students found with unpaid fees.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
        // Print functionality
        function printTable() {
            // Open print view in new window
            const printWindow = window.open(
                "{{ route('admin.unpaid-fees.print') }}?" + new URLSearchParams(new FormData(document.querySelector(
                    'form'))).toString(),
                'PrintWindow',
                'width=1000,height=800'
            );

            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            };
        }
        // Department Chart with amount owed
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        new Chart(departmentCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($statistics['by_department'])->pluck('name')),
                datasets: [{
                    label: 'Number of Defaulters',
                    data: @json(collect($statistics['by_department'])->pluck('count')),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Total Amount Owed (₦)',
                    data: @json(collect($statistics['by_department'])->pluck('total_owed')),
                    backgroundColor: 'rgba(220, 38, 38, 0.5)',
                    borderColor: 'rgb(220, 38, 38)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Defaulters'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Amount Owed (₦)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Level Chart with amount owed
        const levelCtx = document.getElementById('levelChart').getContext('2d');
        new Chart(levelCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($statistics['by_level'])),
                datasets: [{
                    label: 'Number of Defaulters',
                    data: @json(array_map(fn($level) => $level['count'], $statistics['by_level'])),
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Total Amount Owed (₦)',
                    data: @json(array_map(fn($level) => $level['total_owed'], $statistics['by_level'])),
                    backgroundColor: 'rgba(245, 158, 11, 0.5)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Defaulters'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Amount Owed (₦)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    </script>
@endsection
