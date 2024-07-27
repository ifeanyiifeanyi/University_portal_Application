@extends('admin.layouts.admin')

@section('title', 'Course Assignments for ' . $semester->name)

@section('admin')
    <div class="container">
        <h3>Course Assignments for {{ $semester->name }}</h3>
        <h4>{{ $semester->academicSession->name }}
            {{ $semester->academicSession->is_current ? '(Current Academic Session)' : '' }}</h4>
        <hr>

        @php
            $groupedAssignments = $semester->courseAssignments->groupBy('department_id');
        @endphp

        @foreach ($groupedAssignments as $departmentId => $departmentAssignments)
            @php
                $department = $departmentAssignments->first()->department;
            @endphp

            <div class="card mb-4">
                <div class="card-header">
                    <h3>{{ $department->name }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit load</th>
                                <th>Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departmentAssignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->course->code }}</td>
                                    <td>{{ $assignment->course->title }}</td>
                                    <td>{{ $assignment->course->credit_hours }}</td>
                                    <td>{{ $assignment->level }}</td>
                                    <td>
                                        <a href="{{ route('course-assignments.edit', $assignment) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('course-assignments.destroy', $assignment) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if ($groupedAssignments->isEmpty())
            <p>No course assignments found for this semester.</p>
        @endif

        <a href="{{ route('course-assignments.index') }}" class="btn btn-secondary">Back to Overview</a>
    </div>
@endsection
