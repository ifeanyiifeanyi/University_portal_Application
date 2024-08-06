@extends('admin.layouts.admin')

@section('title', 'Student Course Registrations')

@section('admin')
    <div class="container">
        <h2>Course Registrations for {{ $student->user->fullName() }}</h2>

        @foreach ($registrations as $academicSessionId => $semesters)
            <h3>{{ \App\Models\AcademicSession::find($academicSessionId)->name }}</h3>

            @foreach ($semesters as $semesterId => $semesterRegistrations)
                <h4>{{ \App\Models\Semester::find($semesterId)->name }}</h4>

                @foreach ($semesterRegistrations as $registration)
                    <div class="card mb-3">
                        <div class="card-header">
                            Registration Status: {{ $registration->status }}
                            <br>
                            Total Credit Hours: {{ $registration->total_credit_hours }}
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Title</th>
                                        <th>Credit Hours</th>
                                        <th>Status</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registration->courseEnrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->course->code }}</td>
                                            <td>{{ $enrollment->course->title }}</td>
                                            <td>{{ $enrollment->course->credit_hours }}</td>
                                            <td>{{ $enrollment->status }}</td>
                                            <td>{{ $enrollment->grade ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
    </div>
@endsection
