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
                    <h4 class="{{ $isCurrentSession ? 'text-white' : '' }}">{{ $academicSession->name }}
                        {{ $isCurrentSession ? '(Current Academic Session)' : '' }}</h4>
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

                        <div class="shadow card mb-5 {{ $isCurrentSemester ? 'border-success' : '' }}">
                            <div class="card-header {{ $isCurrentSemester ? 'card border-top border-0 border-4 border-primary' : '' }}">
                                <h5 class="{{ $isCurrentSemester ? 'text-muted' : '' }}">{{ $semester->name }}
                                    {{ $isCurrentSemester ? '(Current Semester)' : '' }}</h5>
                            </div>
                            <div class="card-body">
                                <p>Total Assignments: {{ $assignments->count() }}</p>
                                <p>Departments:</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($assignments->pluck('department.name')->unique() as $department)
                                        <div class="chip chip-md bg-info text-white">
                                            {{ $department }}
                                            <span class="closebtn"
                                                onclick="this.parentElement.style.display='none'">
                                                <i class="fas fa-thumbtack"></i>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                <a href="{{ route('course-assignments.show', $semester->id) }}"
                                    class="btn btn-info mt-3">View Details</a>
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
