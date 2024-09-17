@extends('admin.layouts.admin')

@section('title', 'Audits')
@section('css')

@endsection

@section('admin')
    <div class="container">
        <button onclick="history.back()" class="btn mb-2" style="background: rgb(188, 1, 235);color:white">Back</button>
        <div class="card p-3 mb-3">
            <h5>Score Audits: <code>{{ $student->user->first_name }} {{ $student->user->last_name }}</code></h5>
        </div>

        <div class="card p-3 mb-3">
            <form action="{{ route('admin.student.audit', $student) }}" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="academic_session" class="form-control">
                            <option value="">Select Session</option>
                            @foreach ($academicSessions as $id => $name)
                                <option value="{{ $id }}"
                                    {{ request('academic_session') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="semester" class="form-control">
                            <option value="">Select Semester</option>
                            @foreach ($semesters as $id => $name)
                                <option value="{{ $id }}" {{ request('semester') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="course" class="form-control">
                            <option value="">Select Course</option>
                            @foreach ($courses as $id => $title)
                                <option value="{{ $id }}" {{ request('course') == $id ? 'selected' : '' }}>
                                    {{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="student_name" class="form-control" placeholder="Student Name"
                            value="{{ request('student_name') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="teacher_name" class="form-control" placeholder="Teacher Name"
                            value="{{ request('teacher_name') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('admin.student.audit', $student) }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-striped border-dark">
                    <thead class="border-dark">
                        <tr>
                            <th>Session</th>
                            <th>Semester</th>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Teacher</th>
                            <th>Action</th>
                            <th>Changed Fields</th>
                            <th>Manager</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupedAudits as $session => $semesters)
                            @foreach ($semesters as $semester => $courses)
                                @foreach ($courses as $course => $audits)
                                    @foreach ($audits as $audit)
                                        <tr>
                                            <td>{{ $session }}</td>
                                            <td>{{ $semester }}</td>
                                            <td>{{ $course }}</td>
                                            <td>{{ $audit->created_at->format('D, M j, Y g:i A') }}</td>
                                            <td>{{ $audit->teacher_name }}</td>
                                            <td>{{ $audit->action }}</td>
                                            <td>{{ Str::limit($audit->comment, 25) }}</td>
                                            <td>{{ $audit->user->first_name }} {{ $audit->user->last_name }}</td>
                                            {{-- <td>{{ App\Helpers\GeoIPHelper::getLocation($audit->ip_address) }}</td> --}}
                                            <td>{{ $audit->ip_address}}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>



@endsection


@section('javascript')

@endsection
