@extends('admin.layouts.admin')
@section('title', 'Score Audits')
@section('admin')
    <div class="container">
        <div class="card py-3 px-3">
            <form id="auditFilterForm" action="{{ route('admin.score.audit.view') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="academic_session_id">Academic Session:</label>
                        <select id="academic_session_id" name="academic_session_id" class="form-control">
                            <option value="">All Sessions</option>
                            @foreach ($academicSessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="semester_id">Semester:</label>
                        <select id="semester_id" name="semester_id" class="form-control">
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
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="student">Student:</label>
                        <input type="text" id="student" name="student" class="form-control"
                            value="{{ request('student') }}" placeholder="Search by student name">
                    </div>
                    <div class="col-md-3">
                        <label for="course">Course:</label>
                        <input type="text" id="course" name="course" class="form-control"
                            value="{{ request('course') }}" placeholder="Search by course title">
                    </div>
                    <div class="col-md-3">
                        <label for="action">Action:</label>
                        <select id="action" name="action" class="form-control">
                            <option value="">All Actions</option>
                            <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="approve" {{ request('action') == 'approve' ? 'selected' : '' }}>Approve</option>
                            <option value="revert" {{ request('action') == 'approved' ? 'selected' : '' }}>Revert</option>
                            <option value="pending" {{ request('action') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('action') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="user">User:</label>
                        <input type="text" id="user" name="user" class="form-control"
                            value="{{ request('user') }}" placeholder="Search by user name">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Filters</button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.score.audit.view') }}" class="btn btn-secondary"><i class="fas fa-minus-circle"></i> Filters</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.score.audit.export') }}" class="btn btn-success"><i class="fas fa-file-download"></i> Excel</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card py-3 px-3">

            @foreach ($audits as $group => $groupAudits)
                <h5 class="mx-4 my-4">{{ $group }}</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered border-dark" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Action</th>
                                <th>Comment</th>
                                <th>User</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($groupAudits) --}}
                            @foreach ($groupAudits as $audit)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $audit->created_at->format('D, M j, Y g:i A') }}</td>
                                    <td>{{ $audit->studentScore->student->user->fullName() }}</td>
                                    <td>{{ $audit->studentScore->course->title }}</td>
                                    <td>{{ $audit->action }}</td>
                                    {{-- <td>
                                        @if (is_array($audit->changed_fields) || is_object($audit->changed_fields))
                                            @foreach ($audit->changed_fields as $field)
                                                <strong>{{ $field }}:</strong>
                                                <span class="text-danger">{{ $audit->old_value[$field] ?? 'N/A' }}</span> →
                                                <span class="text-success">{{ $audit->new_value[$field] ?? 'N/A' }}</span><br>
                                            @endforeach
                                        @else
                                            <span>No changes recorded</span>
                                        @endif
                                    </td> --}}
                                    <td>{{ Str::limit($audit->comment, 25) }}</td>
                                    <td>{{ $audit->user->fullName() }}</td>
                                    <td>{{ $audit->ip_address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            {{ $audits->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            startDate.addEventListener('change', function() {
                endDate.min = this.value;
            });

            endDate.addEventListener('change', function() {
                startDate.max = this.value;
            });

            const form = document.getElementById('auditFilterForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                for (const pair of formData.entries()) {
                    if (pair[1] === '') {
                        formData.delete(pair[0]);
                    }
                }

                const queryString = new URLSearchParams(formData).toString();
                window.location.href = `${form.action}?${queryString}`;
            });
        });
    </script>
@endsection
