@extends('admin.layouts.admin')

@section('title', 'Semester Manager')

@section('admin')
    <div class="container">
        <h1>Semester Manager</h1>
        <a href="{{ route('semester-manager.create') }}" class="btn btn-primary mb-3">Create New Semester</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Season</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Academic Session</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($semesters as $semester)
                    <tr>
                        <td>
                            {{ $semester->name }}
                            @if ($semester->is_current == true)
                            <p><span class="bg-success p-1 text-light">Current Semester</span></p>

                            @endif
                        </td>
                        <td>{{ $semester->season }}</td>
                        <td>{{ $semester->start_date }}</td>
                        <td>{{ $semester->end_date }}</td>
                        <td>
                            {{ $semester->academicSession->name }}
                            @if ($semester->academicSession->is_current == true)
                                <p><span class="bg-success p-1 text-light">Current Session</span></p>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('semester-manager.edit', $semester) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('semester-manager.destroy', $semester) }}" method="POST" class="d-inline">
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
@endsection
