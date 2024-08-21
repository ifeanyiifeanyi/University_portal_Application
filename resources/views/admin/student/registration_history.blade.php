@extends('admin.layouts.admin')

@section('title', 'Student Registration History')

@section('admin')
    <div class="container">
        <div class="card py-3 p-3">
            <h4>Registration History for {{ $student->user->full_name }}</h4>
            <p><strong>Student ID:</strong> {{ $student->matric_number }}</p>
            <p><strong>Department:</strong> {{ $student->department->name }}</p>
            <p><strong>Date of Admission:</strong> {{ $student->year_of_admission }}</p>
            <span>
                <button onclick="history.back()" class="btn btn-info">Back</button>
            </span>
        </div>
        @foreach ($registrationHistory as $registration)
            <div class="card mb-4">
                <div class="card-header" id="heading{{ $registration->id }}">
                    <h5 class="mb-0">
                        {{ $registration->academicSession->name }} - {{ $registration->semester->name }}

                    </h5>
                    <p><strong>Status:</strong> {{ ucfirst($registration->status) }}</p>
                    <p><strong>Total Credit Hours:</strong> {{ $registration->total_credit_hours }}</p>
                </div>

                <div class="card-body">
                    <h6>Registered Courses</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Credit Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registration->courseEnrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->course->code }}</td>
                                        <td>{{ $enrollment->course->title }}</td>
                                        <td>{{ $enrollment->course->credit_hours }}</td>
                                        <td>{{ ucfirst($enrollment->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
@endsection

@section('javascript')

@endsection
