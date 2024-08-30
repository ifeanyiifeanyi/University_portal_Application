@extends('layouts.public')

@section('content')
<div class="container">
    <h2>Timetable for {{ $department->name }} - Level {{ $level }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Day</th>
                <th>Time</th>
                <th>Course</th>
                <th>Teacher</th>
                <th>Room</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timetables as $timetable)
            <tr>
                <td>{{ $timetable->day_of_week }}</td>
                <td>{{ $timetable->start_time }} - {{ $timetable->end_time }}</td>
                <td>{{ $timetable->course->name }}</td>
                <td>{{ $timetable->teacher->name }}</td>
                <td>{{ $timetable->room }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
