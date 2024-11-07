@extends('admin.layouts.admin')

@section('title', 'Department Details')

@section('admin')
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-12">
                <div class="card-body">


                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form class="d-flex justify-content-center"
                                action="{{ route('admin.department.show', $department->id) }}" method="GET">
                                <div class="d-flex">
                                    <input type="text" class="form-control me-2" name="search" placeholder="Search..."
                                        value="{{ request()->input('search') }}">
                                    <select class="form-select me-2" name="session">
                                        <option value="">Filter by Session</option>
                                        @foreach ($academicSessions as $session)
                                            <option value="{{ $session->name }}"
                                                {{ request()->input('session') == $session->name ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select class="form-select me-2" name="semester">
                                        <option value="">Filter by Semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->name }}"
                                                {{ request()->input('semester') == $semester->name ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select class="form-select me-2" name="level">
                                        <option value="">Filter by Level</option>
                                        @foreach ($department->levels as $level)
                                            <option value="{{ $level }}"
                                                {{ request()->input('level') == $level ? 'selected' : '' }}>
                                                Level {{ $level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h4 class="mb-0">{{ $department->name }}</h4>
                        </div>

                        <div class="card-body">
                            <div class="row mb-4">

                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('admin.department.export-csv', $department->id) }}"
                                        class="btn btn-secondary"> <i class="fas fa-file-csv"></i> Export</a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Course</th>
                                            <th>Teacher</th>
                                            <th>Semester</th>
                                            <th>Academic Session</th>
                                            <th>Level</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assignments as $assignment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $assignment->course->title }}</td>
                                                <td>
                                                    @if ($assignment->teacherAssignments->isNotEmpty())
                                                        @foreach ($assignment->teacherAssignments as $teacherAssignment)
                                                            @if ($teacherAssignment->teacher)
                                                                <a href="{{ route('admin.teacher.show', $teacherAssignment->teacher->id) }}"
                                                                    class="teacher-link">
                                                                    {{ $teacherAssignment->teacher->title_full_name() }}
                                                                </a>
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        Not assigned yet
                                                    @endif
                                                </td>
                                                <td>{{ $assignment->semester->name }}</td>
                                                <td>{{ $assignment->semester->academicSession->name }}</td>
                                                <td>{{ $assignment->level }}</td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No course assignments found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                {{ $assignments->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
