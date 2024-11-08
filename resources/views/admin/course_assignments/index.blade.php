@extends('admin.layouts.admin')

@section('title', 'Course | Academic Session Manager')
@section('css')
    <style>
        .active-session {
            background-color: #f8f9fa;
            border-left: 4px solid #198754;
            /* Bootstrap success color */
            padding: 8px 12px;
            border-radius: 4px;
        }
    </style>
@endsection
@section('admin')
    <div class="container">

        <a href="{{ route('course-assignments.create') }}" class="btn btn-secondary mb-3 mt-3">
            <i class="fas fa-plus-circle"></i>
            Create New
        </a>
        <hr>
        @php
            $groupedAssignments = $assignments->groupBy('semester.academicSession.id');
        @endphp

        @foreach ($groupedAssignments as $academicSessionId => $academicSessionAssignments)
            @php
                $academicSession = $academicSessionAssignments->first()->semester->academicSession;
                $isCurrentSession = $academicSession->is_current;
            @endphp

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="@if ($isCurrentSession) active-session @endif">
                        {{ $academicSession->name }}
                        @if ($isCurrentSession)
                            <i class="fas fa-calendar-check text-success"></i>
                            <small class="text-success fw-light">(Current)</small>
                        @endif
                        <span class="float-end">{{ $academicSessionAssignments->count() }}</span>
                    </h4>
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

                        <div
                            class="card shadow-sm mb-5 {{ $isCurrentSemester ? 'border border-secondary border-opacity-25' : '' }}">
                            <div class="card-header {{ $isCurrentSemester ? 'bg-light' : '' }}">
                                <h5 class="mb-0 d-flex align-items-center">
                                    {{ $semester->name }}
                                    @if ($isCurrentSemester)
                                        <span class="ms-2 text-secondary small">
                                            <i class="fas fa-calendar-check me-1"></i>
                                            Current Semester
                                        </span>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Total Assignments:</strong> {{ $assignments->count() }}
                                </div>

                                <div class="mb-3">
                                    <strong>Departments:</strong>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach ($assignments->pluck('department.name')->unique() as $department)
                                            <div class="badge bg-light text-dark border d-flex align-items-center">
                                                <i class="fas fa-building me-1 text-secondary"></i>
                                                {{ $department }}
                                                <button type="button" class="btn-close ms-2 text-secondary"
                                                    aria-label="Remove" onclick="this.parentElement.style.display='none'">
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <a href="{{ route('course-assignments.show', $semester->id) }}"
                                    class="btn btn-light border shadow-sm">
                                    <i class="fas fa-eye me-1"></i>
                                    View Details
                                </a>
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
