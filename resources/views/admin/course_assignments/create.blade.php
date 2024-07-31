@extends('admin.layouts.admin')

@section('title', isset($assignment) ? 'Edit Course Assignment' : 'Create Course Assignment')

@section('admin')
    <div class="container">
        <h1>Create Course Assignment</h1>
        <div class="row">
            <div class="col-md-6 mx-auto shadow-sm">
                <div class="card-body">
                    <form action="{{ route('course-assignments.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="course_id">Course</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ isset($assignment) && $assignment->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->code }} - {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="department_id">Department</label>
                            <select class="form-control" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ isset($assignment) && $assignment->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="semester_id">Semester</label>
                            <select class="form-control" id="semester_id" name="semester_id" required>
                                <option value="">Select Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>
                                        {{ $semester->name }} {{ $semester->is_current ? '(Current Semester)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="level">Level</label>
                            <select class="form-control" id="level" name="level" required>
                                {{-- <option value="#!">Select Deparament Level</option>
                                @forelse ($department_levels as $department_level)
                                    <option value="{{ $department_level }}">{{ $department_level }}</option>
                                @empty
                                    
                                @endforelse --}}
                            </select>
                        </div>
                        <button type="submit"
                            class="btn btn-primary">{{ isset($assignment) ? 'Update' : 'Create' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            const levelSelect = document.getElementById('level');

            function updateLevels() {
                const departmentId = departmentSelect.value;
                fetch(`/admin/departments/${departmentId}/levels`)
                    .then(response => response.json())
                    .then(levels => {
                        levelSelect.innerHTML = '';
                        levels.forEach(level => {
                            const option = document.createElement('option');
                            option.value = level;
                            option.textContent = level;
                            levelSelect.appendChild(option);
                        });
                    });
            }

            departmentSelect.addEventListener('change', updateLevels);
            updateLevels(); // Initial population
        });
    </script>
@endsection
