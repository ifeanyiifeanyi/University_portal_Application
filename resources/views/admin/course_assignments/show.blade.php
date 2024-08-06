@extends('admin.layouts.admin')

@section('title', 'Course Assignments for ' . $semester->name)

@section('css')
    <style>
        .department-card {
            transition: all 0.3s ease;
        }

        .department-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .level-card {
            transition: all 0.3s ease;
        }

        .level-card:hover {
            transform: translateY(-5px);
        }

        .course-row:hover {
            background-color: #f8f9fa;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1000;
        }
    </style>
@endsection

@section('admin')
    <div class="container-fluid">
        <div class="sticky-header p-3">
            <div class="text-center">
                <h3>Course Assignments for <br> <span class="lead"
                        style="color: rgb(84, 5, 104)">{{ $semester->name }}</span></h3>
                <h4> <code>{{ $semester->academicSession->name }}</code>
                    {{ $semester->academicSession->is_current ? '(Current Academic Session)' : '' }}</h4>
                <hr>
            </div>
            <form action="{{ route('course-assignments.show', $semester->id) }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" class="form-control" placeholder="Search courses..." name="search"
                            value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="department" class="form-control">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $filterDepartment == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="level" class="form-control">
                            <option value="">All Levels</option>
                            @foreach ($levels as $lvl)
                                <option value="{{ $lvl }}" {{ $filterLevel == $lvl ? 'selected' : '' }}>Level
                                    {{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            @forelse ($departments as $department)
                @if (isset($groupedAssignments[$department->id]))
                    @php
                        // Fetch the max credit hours for the department-semester pairing
                        $maxCreditHours =
                            $department
                                ->semesters()
                                ->where('semester_id', $semester->id)
                                ->first()->pivot->max_credit_hours ?? 'N/A';
                    @endphp
                    <div class="col-12 mb-4">
                        <div class="card department-card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="lead text-light">{{ $department->name }}</h4>
                                <p>Max Credit Hours: <strong>{{ $maxCreditHours }}</strong></p>
                            </div>
                            <div class="card-body">
                                @forelse ($groupedAssignments[$department->id] as $level => $levelAssignments)
                                    <div class="card level-card mb-3">
                                        <div class="card-header card border-top border-0 border-4 border-secondary">
                                            <h5 class="lead text-muted">Level {{ $level }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Title</th>
                                                            <th>Credits</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($levelAssignments as $assignment)
                                                            <tr class="course-row">
                                                                <th>{{ $assignment->course->code }}</th>
                                                                <th>{{ $assignment->course->title }}</th>
                                                                <th>{{ $assignment->course->credit_hours }}</th>
                                                                <th>
                                                                    <form
                                                                        action="{{ route('course-assignments.destroy', $assignment) }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button style="background: transparent"
                                                                            type="submit" class="border-0"
                                                                            onclick="return confirm('Are you sure?')">
                                                                            <x-delete-icon />
                                                                        </button>
                                                                    </form>
                                                                </th>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p>No courses assigned for this department.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <p>No course assignments found for this semester.</p>
                </div>
            @endforelse
        </div>

        <a href="{{ route('course-assignments.index') }}" class="btn btn-secondary mt-3">Back to Overview</a>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('form');
            const inputs = searchForm.querySelectorAll('input, select');

            inputs.forEach(input => {
                input.addEventListener('change', () => searchForm.submit());
            });
        });
    </script>
@endsection
