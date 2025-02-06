@extends('admin.layouts.admin')

@section('title', isset($assignment) ? 'Edit Department Assigned Course' : 'Assign Course To Departments')

@section('admin')
    <div class="container">
        @include('admin.alert')
        <div class="row">
            <div class="col-md-6 mx-auto shadow-lg mb-5">
                <div class="card-body">
                    <h5 class="text-center">@yield('title')</h5>
                    <form action="{{ route('course-assignments.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="course_id">Course</label>
                            <select class="form-control single-select" id="course_id" name="course_id" required>
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
                            <label for="academic_session_id">Academic Session</label>
                            <select class="form-control single-select" id="academic_session_id" name="academic_session_id"
                                required>
                                <option value="">Select Academic Session</option>
                                @foreach ($academicSessions as $session)
                                    <option value="{{ $session->id }}" {{ $session->is_current ? 'selected' : '' }}>
                                        {{ $session->name }}
                                        {{ $session->is_current ? '(Current Academic Session)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="semester_id">Semester</label>
                            <select class="form-control single-select" id="semester_id" name="semester_id" required>
                                <option value="">Select Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>
                                        {{ $semester->name }} {{ $semester->is_current ? '(Current Semester)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="level">Academic Level</label>
                            <select class="form-control" id="level" name="level" required>
                                {{-- <option value="#!">Select Deparament Level</option>
                                @forelse ($department_levels as $department_level)
                                    <option value="{{ $department_level }}">{{ $department_level }}</option>
                                @empty

                                @endforelse --}}
                            </select>
                        </div>



                        <button type="submit" class="btn btn-secondary btn-sm"><i
                                class="fas fa-save"></i>{{ isset($assignment) ? 'Update' : 'Create' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     const departmentSelect = document.getElementById('department_id');
        //     const levelSelect = document.getElementById('level');

        //     function updateLevels() {
        //         const departmentId = departmentSelect.value;

        //         if (!departmentId) {
        //             levelSelect.innerHTML = '<option value="">Select Department First</option>';
        //             levelSelect.disabled = true;
        //             return;
        //         }

        //         levelSelect.innerHTML = '<option value="">Loading levels...</option>';
        //         levelSelect.disabled = true;

        //         fetch(`/admin/departments/${departmentId}/levels`)
        //             .then(response => response.json())
        //             .then(levels => {
        //                 levelSelect.innerHTML = '';
        //                 levels.forEach(level => {
        //                     const option = document.createElement('option');
        //                     option.value = level;
        //                     option.textContent = level;
        //                     levelSelect.appendChild(option);
        //                 });
        //             });
        //     }

        //     departmentSelect.addEventListener('change', updateLevels);
        //     updateLevels(); // Initial population
        // });

        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            const levelSelect = document.getElementById('level');

            function updateLevels() {
                const departmentId = departmentSelect.value;
                if (!departmentId) {
                    levelSelect.innerHTML = '<option value="">Select Department First</option>';
                    levelSelect.disabled = true;
                    return;
                }

                levelSelect.innerHTML = '<option value="">Loading levels...</option>';
                levelSelect.disabled = true;

                fetch(`/admin/departments/${departmentId}/levels`)
                    .then(response => response.json())
                    .then(levels => {
                        levelSelect.innerHTML = '<option value="">Select Level</option>';

                        levels.forEach(level => {
                            const option = document.createElement('option');

                            // Set the display text to show the format-specific level
                            option.textContent = level;

                            // Map the display format to numeric values
                            if (typeof level === 'string' && (level.startsWith('RN') ||
                                    level.startsWith('ND') || level.startsWith('HND') ||
                                    level.startsWith('RMW'))) {
                                switch (level) {
                                    case 'RN1':
                                    case 'ND1':
                                    case 'RMW1':
                                        option.value = '100';
                                        break;
                                    case 'RN2':
                                    case 'ND2':
                                    case 'RMW2':
                                        option.value = '200';
                                        break;
                                    case 'RN3':
                                    case 'RMW3':
                                        option.value = '300';
                                        break;
                                    case 'HND1':
                                        option.value = '300';
                                        break;
                                    case 'HND2':
                                        option.value = '400';
                                        break;
                                    default:
                                        option.value = level;
                                }
                            } else {
                                // For numeric levels, use the level as is
                                option.value = level;
                            }

                            levelSelect.appendChild(option);
                        });
                        levelSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading levels:', error);
                        levelSelect.innerHTML = '<option value="">Error loading levels</option>';
                        levelSelect.disabled = true;
                    });
            }

            departmentSelect.addEventListener('change', updateLevels);
            if (departmentSelect.value) {
                updateLevels();
            }
        });
    </script>
@endsection
