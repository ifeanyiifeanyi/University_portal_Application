@extends('admin.layouts.admin')

@section('title', 'Course Registration Management')

@section('css')

    <style>
        .stat-card {
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            transition: transform 0.3s ease-in-out;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .total-gradient {
            background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
        }

        .pending-gradient {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }

        .approved-gradient {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
        }

        .rejected-gradient {
            background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%);
        }

        .card-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-text {
            font-weight: bold;
            margin-top: 0.5rem;
        }

        /* Shimmer effect */
        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to right,
                    transparent 0%,
                    rgba(255, 255, 255, 0.2) 50%,
                    transparent 100%);
            transform: rotate(30deg);
            transition: transform 0.7s ease-in-out;
            opacity: 0;
        }

        .stat-card:hover::after {
            opacity: 1;
            transform: rotate(30deg) translate(50%, -50%);
        }
    </style>

@endsection

@section('admin')
    <div class="container">

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card total-gradient text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-white-50">Registrations</h5>
                                <p class="card-text h2 mb-0">{{ $stats['total'] }}</p>
                            </div>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card pending-gradient text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-white-50">Pending</h5>
                                <p class="card-text h2 mb-0">{{ $stats['pending'] }}</p>
                            </div>
                            <i class="fas fa-clock stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card approved-gradient text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-white-50">Approved</h5>
                                <p class="card-text h2 mb-0">{{ $stats['approved'] }}</p>
                            </div>
                            <i class="fas fa-check-circle stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card rejected-gradient text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-white-50">Rejected</h5>
                                <p class="card-text h2 mb-0">{{ $stats['rejected'] }}</p>
                            </div>
                            <i class="fas fa-times-circle stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card py-3 px-3">
            <form action="{{ route('admin.students.all-course-registrations') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <select name="department_id" class="form-control">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="academic_session_id" class="form-control">
                            <option value="">All Academic Sessions</option>
                            @foreach ($academicSessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="semester_id" class="form-control">
                            <option value="">All Semesters</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}"
                                    {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"
                            placeholder="Start Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"
                            placeholder="End Date">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Search by student name, ID, session, or semester">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="mt-4">
                <a href="{{ route('admin.course-registrations.export') }}" class="btn btn-secondary"><i
                        class="fas fa-file-csv"></i> Export</a>
            </div>
        </div>
        <div class="card py-3 px-3">
            <div class="table-responsive">
                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>sn</th>
                            <th>MAT ID</th>
                            <th>Student</th>
                            <th>Department</th>
                            <th>Academic Session</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @dd($registrations) --}}
                        @foreach ($registrations as $registration)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $registration->student->matric_number }}</td>
                                <td>{{ $registration->student->user->full_name }}</td>
                                <td>{{ $registration->student->department->name }}</td>
                                <td>{{ $registration->academicSession->name }}</td>
                                <td>{{ $registration->semester->name }}</td>
                                <td>{{ ucfirst($registration->status) }}</td>
                                <td>
                                    <a href="{{ route('admin.course-registrations.show', $registration) }}"
                                        class="btn btn-sm text-info"><i class="fas fa-eye"></i></a>

                                    @if ($registration->status == 'approved')
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.reject', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm text-danger"><i
                                                    class="fas fa-times"></i></button>
                                        </form>
                                    @endif

                                    @if ($registration->status == 'rejected')
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.approve', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm text-success"><i
                                                    class="fas fa-thumbs-up"></i></button>
                                        </form>
                                    @endif
                                    @if ($registration->status == 'pending')
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.approve', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm text-success"><i
                                                    class="fas fa-thumbs-up"></i></button>
                                        </form>
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.reject', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm text-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- {{ $registrations->links() }} --}}
            {!! $registrations->links('pagination::bootstrap-4') !!}
        </div>
        <div class="card py-3 px-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mt-4">
                        <h4>Top Departments</h4>
                        <ul>
                            @foreach ($topDepartments as $department)
                                <li>{{ $department->name }}: {{ $department->students_count }} registrations</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mt-4">
                        <h4>Top Departments by Course Registration</h4>
                        <ul>
                            @foreach ($topDepartments as $department)
                                <li>{{ $department->name }}:
                                    {{ $department->students_semester_course_registrations_count }} registrations</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('javascript')
    <!-- Include any additional JavaScript needed for your design -->
    <script>
        // Optional: Add JavaScript here if needed for interactivity
    </script>
@endsection
