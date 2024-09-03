@extends('admin.layouts.admin')

@section('title', 'conflicts')

@section('admin')
    <h1>Timetable Conflicts</h1>

    @if(count($conflicts) > 0)
        @foreach($conflicts as $conflict)
            <div class="card mb-3">
                <div class="card-header">
                    Conflict for: {{ $conflict['entry']->course->title }} ({{ Timetable::getDayName($conflict['entry']->day_of_week) }} {{ $conflict['entry']->start_time->format('H:i') }} - {{ $conflict['entry']->end_time->format('H:i') }})
                </div>
                <div class="card-body">
                    <h5 class="card-title">Conflicting Entries:</h5>
                    <ul>
                        @foreach($conflict['conflicting_entries'] as $conflictingEntry)
                            <li>
                                {{ $conflictingEntry->course->title }}
                                ({{ Timetable::getDayName($conflictingEntry->day_of_week) }}
                                {{ $conflictingEntry->start_time->format('H:i') }} - {{ $conflictingEntry->end_time->format('H:i') }})
                                <br>
                                Teacher: {{ $conflictingEntry->teacher->user->name }}, Room: {{ $conflictingEntry->room }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-success">No conflicts found in the timetable.</div>
    @endif
@endsection
