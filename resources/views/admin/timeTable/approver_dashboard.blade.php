@extends('admin.layouts.admin')

@section('admin')
<div class="container">
    <h2>Timetable Approver Dashboard</h2>
    <form action="{{ route('admin.timetables.bulk-approve') }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>Academic Session</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingTimetables as $timetable)
                <tr>
                    <td><input type="checkbox" name="timetable_ids[]" value="{{ $timetable->id }}"></td>
                    <td>{{ $timetable->department->name }}</td>
                    <td>{{ $timetable->semester->name }}</td>
                    <td>{{ $timetable->academicSession->name }}</td>
                    <td>
                        <a href="{{ route('admin.timetables.show', $timetable->id) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Approve Selected</button>
    </form>
</div>
@endsection
