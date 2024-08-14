@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <h5>Course Registration Details</h5>
        {{-- @dd($registration) --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Student Information</h5>
                <p><strong>Name:</strong> {{ $registration->student->user->full_name }}</p>
                <p><strong>Student ID:</strong> {{ $registration->student->matric_number }}</p>
                <p><strong>Department:</strong> {{ $registration->student->department->name }}</p>
                <p><strong>Academic Session:</strong> {{ $registration->academicSession->name }}</p>
                <p><strong>Semester:</strong> {{ $registration->semester->name }}</p>
                <p><strong>Status:</strong> {{ ucfirst($registration->status) }}</p>
                <p><strong>Total Credit Hours:</strong> {{ $registration->total_credit_hours }}</p>
            </div>
        </div>

        <div class="card py-3 px-3">
            <h4 class="mt-4">Registered Courses</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit Hours</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registration->courseEnrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->course->code }}</td>
                                <td>{{ $enrollment->course->title }}</td>
                                <td>{{ $enrollment->course->credit_hours }}</td>
                                <td>{{ $enrollment->registered_at->format('jS F Y, g:ia') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($registration->status == 'pending')
                <div class="mt-4">
                    <form onsubmit="return confirm('Are sure of this action')"
                        action="{{ route('admin.course-registrations.approve', $registration) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">Approve Registration</button>
                    </form>
                    <form onsubmit="return confirm('Are sure of this action')"
                        action="{{ route('admin.course-registrations.reject', $registration) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">Reject Registration</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('javascript')

@endsection
