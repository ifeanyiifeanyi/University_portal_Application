@extends('admin.layouts.admin')

@section('title', 'Edit Assignment')
@section('css')

@endsection

@section('admin')
@include('admin.return_btn')

    <div class="container">
        <div class="row">
            <div class="card col-md-8 px-2 py-3 mx-auto">
                <form action="{{ route('admin.teacher.assignment.update', $assignment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label for="teacher_id">Select Lecturer:</label>
                        <select name="teacher_id" id="teacher_id" class="form-control single-select">
                            <option value="">Select Lecturer</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->user->id }}"
                                    {{ $assignment->teacher->user->id == $teacher->user->id ? 'selected' : '' }}>
                                    {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <i class="text-danger">{{ $message }}</i>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="academic_session_id">Academic Session:</label>
                        <select name="academic_session_id" id="academic_session_id" class="form-control single-select">
                            <option value="{{ $currentAcademicSession->id }}" selected>{{ $currentAcademicSession->name }}
                                (Current Session)</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="semester_id">Semester:</label>
                        <select name="semester_id" id="semester_id" class="form-control single-select">
                            <option value="{{ $currentSemester->id }}" selected>{{ $currentSemester->name }} (Current
                                Semester)</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="department_id">Department:</label>
                        <select name="department_id" id="department_id" class="form-control">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ $assignment->department_id == $department->id ? 'selected' : '' }}>
                                    {{ $department->code . ': ' . $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <i class="text-danger">{{ $message }}</i>
                        @enderror
                    </div>

                    <div id="courses-container" style="display: {{ $assignment->department_id ? 'block' : 'none' }};">
                        <h3>Courses:</h3>
                        <div id="course-list">
                            {{-- The courses will be loaded here dynamically --}}
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update Assignment</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            function loadCourses(departmentId, semesterId) {
                if (departmentId && semesterId) {
                    $.ajax({
                        url: "{{ route('admin.get-department-courses') }}",
                        method: 'GET',
                        data: {
                            department_id: departmentId,
                            semester_id: semesterId,
                            assignment_id: "{{ $assignment->id }}"
                        },
                        success: function(response) {
                            $('#course-list').empty();
                            $.each(response, function(index, course) {
                                var levels = course.course_assignments.map(function(ca) {
                                    return ca.level;
                                });

                                // Filter out duplicate levels
                                levels = [...new Set(levels)].join(', ');

                                $('#course-list').append(
                                    '<div class="form-check">' +
                                    '<input class="form-check-input" type="checkbox" name="course_ids[]" value="' +
                                    course.id + '" id="course-' + course.id + '" ' +
                                    (course.is_assigned ? 'checked' : '') + '>' +
                                    '<label class="form-check-label" for="course-' + course
                                    .id + '">' +
                                    course.code + ' - ' + course.title +
                                    ' (Levels: ' + levels + ')' +
                                    '</label>' +
                                    '</div>'
                                );
                            });
                            $('#courses-container').show();
                        }
                    });
                } else {
                    $('#courses-container').hide();
                }
            }

            // Load courses on page load if department and semester are already selected
            loadCourses($('#department_id').val(), $('#semester_id').val());

            // Load courses when the department or semester is changed
            $('#department_id, #semester_id').change(function() {
                loadCourses($('#department_id').val(), $('#semester_id').val());
            });
        });
    </script>
@endsection
