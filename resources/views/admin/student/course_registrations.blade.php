@extends('admin.layouts.admin')

@section('title', 'Registered Courses')

@section('admin')
    <div class="container">
        <h2>Registered Courses for {{ $student->user->fullName() }}</h2>
        <p>Academic Session: {{ $currentAcademicSession->name }}</p>
        <p>Semester: {{ $currentSemester->name }}</p>
        <p>Total Credit Hours: {{ $totalCreditHours }}</p>

        <form action="{{ route('admin.students.approve-registration', $student->id) }}" method="POST">
            @csrf
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Credit Hours</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enrolledCourses as $enrollment)
                        <tr>
                            <td>{{ $enrollment->course->code }}</td>
                            <td>{{ $enrollment->course->title }}</td>
                            <td>{{ $enrollment->course->credit_hours }}</td>
                            <td>
                                <a href="{{ route('admin.students.remove-course', [$student->id, $enrollment->id]) }}"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to remove this course?')">
                                    Remove
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="form-group">
                <label for="status">Registration Status:</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" {{ $semesterRegistration->status == 'pending' ? 'selected' : '' }}>Pending
                    </option>
                    <option value="approved" {{ $semesterRegistration->status == 'approved' ? 'selected' : '' }}>Approved
                    </option>
                    <option value="rejected" {{ $semesterRegistration->status == 'rejected' ? 'selected' : '' }}>Rejected
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Registration Status</button>
        </form>
    </div>
@endsection
