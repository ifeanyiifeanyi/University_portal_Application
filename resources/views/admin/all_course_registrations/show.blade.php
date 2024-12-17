@extends('admin.layouts.admin')

@section('title', 'Course Registration Details')

@section('admin')
    <div class="container">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Student Information</h5>
                <p><strong>Name:</strong> {{ $registration->student->user->full_name }}</p>
                <p><strong>Student ID:</strong> {{ $registration->student->matric_number }}</p>
                <p><strong>Department:</strong> {{ $registration->student->department->name }}</p>

            </div>
        </div>

        @foreach ($allRegistrations as $index => $reg)
            <div class="card mb-4">
                <div class="card-header" id="heading{{ $index }}">
                    <h5 class="mb-0">
                        {{ $reg->academicSession->name }} - {{ $reg->semester->name }}
                    </h5>
                    <p><strong>Status:</strong> {{ ucfirst($reg->status) }}</p>
                    <p><strong>Total Credit Hours:</strong> {{ $reg->total_credit_hours }}</p>
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
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reg->courseEnrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->course->code }}</td>
                                        <td>{{ $enrollment->course->title }}</td>
                                        <td>{{ $enrollment->course->credit_hours }}</td>
                                        <td>{{ $enrollment->created_at->format('jS F Y, g:ia') ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($reg->id === $registration->id)
                        <div class="mt-4">
                            <form onsubmit="return confirm('Are you sure of this action?')"
                                action="{{ route('admin.course-registrations.approve', $reg) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Approve Registration</button>
                            </form>
                            <form onsubmit="return confirm('Are you sure of this action?')"
                                action="{{ route('admin.course-registrations.reject', $reg) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">Reject Registration</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('javascript')

@endsection
