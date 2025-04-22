@extends('admin.layouts.admin')

@section('title', 'Students Manager')
@section('css')
    <style>
        .analytics-card {
            border-radius: 10px;
            transition: all 0.3s;
        }

        .analytics-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            height: 300px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('admin')
    <div class="container-fluid">
        <!-- Analytics Dashboard Section -->
        <div class="row mb-4">
            <!-- BEGIN: Analytics Dashboard -->
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <h5 class="text-muted"><i class="fas fa-chart-line"></i> Student Analytics</h5>
                </div>

                <!-- Summary Cards -->
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="mb-0">{{ $studentStats['total'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Students</h5>
                            <h2 class="mb-0">{{ $studentStats['active'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">New This Month</h5>
                            <h2 class="mb-0">{{ $studentStats['newThisMonth'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Departments</h5>
                            <h2 class="mb-0">{{ $studentStats['departments'] }}</h2>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Students by Department</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="departmentChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Students by Level</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="levelChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Admission Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="admissionTrendChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Analytics Dashboard -->

        </div>

        <!-- Original Student Table Section -->
        <div class="card shadow">
            <div class="card-body">
                <div class="card-title d-flex align-items-center gap-5">
                    <div>
                        <a href="{{ route('admin.student.create') }}" class="btn btn-sm bg-secondary text-white float-right"
                            style="text-align: right"><i class="fas fa-user-plus"></i> Create</a>

                        <a href="{{ route('admin.student.email.bulk') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-envelope"></i> Bulk Email
                        </a>
                        <!-- Download Template Buttons -->
                        <div class="dropdown d-inline">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i>Template
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.students.template.download', ['format' => 'excel']) }}">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.students.template.download', ['format' => 'pdf']) }}">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Import Button -->
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="fas fa-file-import"></i> Import
                        </button>

                        <!-- Import Modal -->
                        <div class="modal fade" id="importModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Import Students</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.students.import_verify') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Department</label>
                                                <select name="department_id" class="form-select" required>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">
                                                            {{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Excel File</label>
                                                <input type="file" name="file" class="form-control"
                                                    accept=".xlsx,.xls" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Verify Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                {{-- getDisplayLevel --}}
                <hr>
                <div class="table-responsive">
                    <table id="example" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Phone/Email</th>
                                <th scope="col">Matr No.</th>
                                <th scope="col">Department</th>
                                <th scope="col">Admission</th>
                                <th scope="col">Level</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>
                                        <div class="d-flex align-items-center">

                                            <img src="{{ $student->user->profile_image }}"
                                                alt="{{ $student->full_name }}" class="rounded-circle me-2"
                                                width="40" height="40">
                                            <div>
                                                {{ $student->user->fullName() ?? '' }}
                                                <br>
                                                Added at {{ $student->created_at->format('jS F Y, g:ia') }}
                                            </div>
                                        </div>

                                    </td>
                                    <td>
                                        {{ $student->user->phone }} <br>
                                        {{ $student->user->email }}
                                    </td>

                                    <td class="text-center">
                                        <code style="font-weight: 900">{{ $student->matric_number }}</code> <br>
                                        <a href="{{ route('admin.student.idcard', $student) }}"
                                            class="badge bg-secondary"><i class="fas fa-id-card"></i></a>
                                    </td>
                                    <td>
                                        {{ $student->department->name }} <br>
                                        <small class="text-muted">({{ $student->department->faculty->name }})</small>
                                    </td>
                                    <td>
                                        <p class="float-end">
                                            <i class="bx bx-calendar me-0"></i>
                                            {{ $student->year_of_admission }}
                                        </p>

                                    </td>
                                    <td>
                                        <div class="float-end">
                                            <i class="bx bx-user me-0"></i>
                                            {{ $student->department->getDisplayLevel($student->current_level) }}
                                        </div>
                                    </td>
                                    <td scope="row">
                                        <div class="col float-end">
                                            <div class="dropdown">
                                                <span class=" dropdown-toggle text-secondary" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{-- <x-menu-icon /> --}}
                                                </span>
                                                <ul class="dropdown-menu custom-dropdown-menu"
                                                    style="text-align: justify">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.student.email.single', $student) }}">
                                                            <i class="bx bx-envelope me-0"></i> Send Email
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.students.course-registrations', $student) }}">
                                                            <i class="bx bx-book-add me-0"></i>
                                                            View Course Registrations
                                                        </a>
                                                    </li>

                                                    <li class="dropdown-divider mb-0"> </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.assign.courseForStudent', $student) }}">
                                                            <i class="bx bx-book-add me-0"></i> Register Courses
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider mb-0"> </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.students.registration-history', $student) }}">
                                                            <i class="bx bx-book-add me-0"></i> Registered Courses
                                                            History
                                                        </a>
                                                    </li>

                                                    <li class="dropdown-divider mb-0"> </li>


                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.student.edit', $student) }}">
                                                            <i class="bx bx-edit me-0"></i> Edit

                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider mb-0"> </li>

                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.student.details', $student) }}">
                                                            <i class="bx bx-coin-stack me-0"></i> View Details

                                                        </a>
                                                    </li>

                                                    <li class="dropdown-divider mb-2"> </li>


                                                    <li>
                                                        <form
                                                            action="{{ route('admin.student.delete', $student->user->id) }}"
                                                            method="post" class="delete-student-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item bg-danger text-light"
                                                                type="submit">
                                                                <i class="bx bx-trash-alt me-0"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('javascript')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
        // Existing delete confirmation script
        document.addEventListener('click', function(event) {
            if (event.target.closest('.delete-student-form')) {
                event.preventDefault();

                if (confirm('Are you sure you want to delete this student?')) {
                    event.target.closest('.delete-student-form').submit();
                }
            }
        });

        // Chart Initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Department Distribution Chart
            const departmentCtx = document.getElementById('departmentChart').getContext('2d');
            const departmentChart = new Chart(departmentCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($chartData['departments']['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['departments']['data']) !!},
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                            '#5a5c69', '#858796', '#6f42c1', '#fd7e14', '#20c997'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });

            // Level Distribution Chart
            const levelCtx = document.getElementById('levelChart').getContext('2d');
            const levelChart = new Chart(levelCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['levels']['labels']) !!},
                    datasets: [{
                        label: 'Number of Students',
                        data: {!! json_encode($chartData['levels']['data']) !!},
                        backgroundColor: '#36b9cc',
                        borderColor: '#2c9faf',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Admission Trend Chart
            const trendCtx = document.getElementById('admissionTrendChart').getContext('2d');
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['admissionTrends']['labels']) !!},
                    datasets: [{
                        label: 'Admissions',
                        data: {!! json_encode($chartData['admissionTrends']['data']) !!},
                        fill: false,
                        borderColor: '#4e73df',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
