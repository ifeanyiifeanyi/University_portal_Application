@extends('admin.layouts.admin')

@section('title', 'Course | Academic Session Manager')

@section('admin')
    <div class="container">
        <h1>Semester Manager</h1>
        <a href="{{ route('course-assignments.create') }}" class="btn btn-primary mb-3">Create New Assignment</a>
        <hr>
        @php
            $groupedAssignments = $assignments->groupBy('semester.academicSession.id');
        @endphp

        @foreach ($groupedAssignments as $academicSessionId => $academicSessionAssignments)
            @php
                $academicSession = $academicSessionAssignments->first()->semester->academicSession;
                $isCurrentSession = $academicSession->is_current;
            @endphp

            <div class="card mb-4 {{ $isCurrentSession ? 'border-primary' : '' }}">
                <div class="card-header {{ $isCurrentSession ? 'bg-primary text-white' : '' }}">
                    <h2 class="{{ $isCurrentSession ? 'text-white' : '' }}">{{ $academicSession->name }} {{ $isCurrentSession ? '(Current Academic Session)' : '' }}</h2>
                </div>
                <div class="card-body">
                    @php
                        $semesterAssignments = $academicSessionAssignments->groupBy('semester_id');
                    @endphp

                    @foreach ($semesterAssignments as $semesterId => $assignments)
                        @php
                            $semester = $assignments->first()->semester;
                            $isCurrentSemester = $semester->is_current;
                        @endphp

                        <div class="card mb-3 {{ $isCurrentSemester ? 'border-success' : '' }}">
                            <div class="card-header {{ $isCurrentSemester ? 'bg-success text-white' : '' }}">
                                <h3 class="{{ $isCurrentSemester ? 'text-white' : '' }}">{{ $semester->name }} {{ $isCurrentSemester ? '(Current Semester)' : '' }}</h3>
                            </div>
                            <div class="card-body">
                                <p>Total Assignments: {{ $assignments->count() }}</p>
                                <p>Departments: {{ $assignments->pluck('department.name')->unique()->implode(', ') }}</p>
                                <a href="{{ route('course-assignments.show', $semester->id) }}" class="btn btn-info">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if ($groupedAssignments->isEmpty())
            <p>No course assignments found.</p>
        @endif
    </div>
@endsection