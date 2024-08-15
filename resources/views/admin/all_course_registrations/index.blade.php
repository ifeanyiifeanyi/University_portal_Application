@extends('admin.layouts.admin')

@section('title', 'Registered Courses')

@section('css')

@endsection

@section('admin')
    <div class="container">
        <h4>Course Registration Management</h4>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Registrations</h5>
                        <p class="card-text">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <p class="card-text">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Approved</h5>
                        <p class="card-text">{{ $stats['approved'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Rejected</h5>
                        <p class="card-text">{{ $stats['rejected'] }}</p>
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
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <div class="mt-4">
                <a href="{{ route('admin.course-registrations.export') }}" class="btn btn-success">Export to CSV</a>
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
                                        class="btn btn-sm btn-info">View</a>

                                    @if ($registration->status == 'approved')
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.reject', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @endif

                                    @if ($registration->status == 'rejected')
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.approve', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                    @endif
                                    @if ($registration->status == 'pending')
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.approve', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form onsubmit="return confirm('Are sure of this action')"
                                            action="{{ route('admin.course-registrations.reject', $registration) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
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
