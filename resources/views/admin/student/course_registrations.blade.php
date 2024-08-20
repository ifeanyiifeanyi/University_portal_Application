@extends('admin.layouts.admin')

@section('title', 'Registered Courses')

@section('admin')
    <div class="container">
        <div class="card px-3 py-3">
            <h4>Registered Courses for {{ $student->user->fullName() }}</h4>
            <p>Academic Session: {{ $currentAcademicSession->name }}</p>
            <p>Semester: {{ $currentSemester->name }}</p>
            <p>Total Credit Hours: {{ $totalCreditHours }}</p>
            <p>Semester Max credit load: {{ $maxCreditHours }}</p>
            <p>Remaining: <code>{{ $maxCreditHours - $totalCreditHours }}</code></p>
            <span>
                <a href="{{ route('admin.assign.courseForStudent', $student->id) }}" class="btn btn-success">
                    Enroll in New Course
                </a>
            </span>
        </div>

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

            <div class="row">

                <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label for="status">Student Registered Courses Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" {{ $semesterRegistration->status == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="approved" {{ $semesterRegistration->status == 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>
                            <option value="rejected" {{ $semesterRegistration->status == 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Registration Status</button>
        </form>
    </div>
@endsection
