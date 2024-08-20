@extends('admin.layouts.admin')

@section('title', 'Assign Semester Credit For Department Levels')

@section('admin')
    <div class="container">
        <h2>Department Credit Loads</h2>
        <a href="{{ route('admin.department.credit.create') }}" class="btn btn-primary mb-3">Assign New Credit Load</a>

        @if ($creditAssignments->isEmpty())
            <div class="alert alert-info">No Department has been assigned a Credit Load</div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Level</th>
                            <th>Max Credit Hours</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($creditAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->department_name }}</td>
                                <td>{{ $assignment->semester_name }}</td>
                                <td>{{ $assignment->level }}</td>
                                <td>{{ $assignment->max_credit_hours }}</td>
                                <td>
                                    <a href="{{ route('admin.department.credit.edit', $assignment->id) }}"
                                        class="btn btn-sm btn-info">Edit</a>
                                    <form onsubmit="return confirm('Are you sure of this action ?')"
                                        action="{{ route('admin.department.credit.delete', $assignment->id) }}"
                                        method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@section('javascript')
@endsection
