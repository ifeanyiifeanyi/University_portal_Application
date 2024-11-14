@extends('admin.layouts.admin')

@section('title', 'Registered Courses')

@section('admin')
    <div class="container">
        @include('admin.alert')
        <div class="card py-3 px-3">
            <h4>Registered Courses for {{ $student->user->fullName() }} | Level: {{ $student->current_level }}</h4>
            <p>Academic Session: {{ $currentAcademicSession->name ?? '' }}</p>
            <p>Semester: {{ $currentSemester->name ?? '' }}</p>
            <p>Department: {{ $enrolledCourses->first()->department->name ?? '' }}</p>
            <p>Faculty: {{ $enrolledCourses->first()->department->faculty->name ?? '' }}</p>
            <p>Total Credit Hours: {{ $totalCreditHours }} / {{ $maxCreditHours }}</p>
            <p>Registration Status: {{ ucfirst($semesterRegistration->status) }}</p>
            <span>
                <a href="{{ route('admin.assign.courseForStudent', $student) }}" class="btn btn-primary">Continue
                    Registration</a></span>

        </div>

        <div class="card py-3 px-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit Hours</th>
                            <th>Admin Approved</th>
                            <th>Status</th>
                            <th>Status Change</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @dd($enrolledCourses) --}}
                        @foreach ($enrolledCourses as $enrollment)
                            <tr>
                                <td>{{ $enrollment->course->code }}</td>
                                <td>{{ $enrollment->course->title }}</td>
                                <td>{{ $enrollment->course->credit_hours }}</td>
                                <td>{{ Str::upper($enrollment->semesterCourseRegistration->status) }}</td>
                                <td>{{ ucfirst($enrollment->status) }}</td>
                                <td>
                                    <form
                                        action="{{ route('admin.students.update-course-status', [$student->id, $enrollment->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-control form-control-sm"
                                            onchange="if(confirm('Are you sure you want to change the status?')) { this.form.submit(); }">
                                            <option value="">Select option</option>
                                            <option value="enrolled"
                                                {{ $enrollment->status == 'enrolled' ? 'selected' : '' }}>
                                                Enrolled</option>
                                            <option value="completed"
                                                {{ $enrollment->status == 'completed' ? 'selected' : '' }}>
                                                Completed</option>
                                            <option value="withdrawn"
                                                {{ $enrollment->status == 'withdrawn' ? 'selected' : '' }}>
                                                Withdrawn</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form
                                        action="{{ route('admin.students.remove-course', [$student->id, $enrollment->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to remove this course?')">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <form action="{{ route('admin.students.approve-registration', $student->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="status">Update All Registered Course Status:</label>
                            <select name="status" id="status" class="form-control"
                                onchange="if(confirm('Are you sure you want to Update all Course status?')) { this.form.submit(); }">
                                <option value="pending" {{ $semesterRegistration->status == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="approved"
                                    {{ $semesterRegistration->status == 'approved' ? 'selected' : '' }}>
                                    Approved
                                </option>
                                <option value="rejected"
                                    {{ $semesterRegistration->status == 'rejected' ? 'selected' : '' }}>
                                    Rejected
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
