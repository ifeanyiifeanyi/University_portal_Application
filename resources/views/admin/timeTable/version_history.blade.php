@extends('admin.layouts.admin')

@section('admin')
<div class="container">
    <h2>Version History for Timetable #{{ $timetable->id }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Version</th>
                <th>Changed At</th>
                <th>Changed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($versions as $version)
            <tr>
                <td>{{ $version->id }}</td>
                <td>{{ $version->created_at }}</td>
                <td>{{ $version->user->name }}</td>
                <td>
                    <a href="{{ route('admin.timetables.show', ['id' => $timetable->id, 'version' => $version->id]) }}" class="btn btn-sm btn-info">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
