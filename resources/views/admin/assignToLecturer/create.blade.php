@extends('admin.layouts.admin')

@section('title', 'Assign Lecturer Department')
@section('css')

@endsection

@section('admin')
    <div class="container">
        <div class="row">
            <div class="card col-md-8 px-2 py-3 mx-auto">
                <form action="{{ route('admin.teacher.assignment.store') }}" method="POST">
                    @csrf

                    @if ($teacher !== null)
                        <h2 class="text-muted mx-3 my-3">Assign Department and Courses to
                            {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}</h2>
                    @else
                        <div class="form-group mb-4">
                            <label for="teacher_id">Select Lecturer:</label>
                            <select name="teacher_id" id="teacher_id" class="form-control single-select" >
                                <option value="">Select Lecturer</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->user->id }}">
                                        {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <i class="text-danger">{{ $message }}</i>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <label for="academic_session_id">Academic Session:</label>
                        <select name="academic_session_id" id="academic_session_id" class="form-control single-select"
                            >
                            <option value="{{ $currentAcademicSession->id }}" selected>{{ $currentAcademicSession->name }}
                                (Current Session)</option>
                        </select>

                    </div>

                    <div class="form-group mb-4">
                        <label for="semester_id">Semester:</label>
                        <select name="semester_id" id="semester_id" class="form-control single-select" >
                            <option value="{{ $currentSemester->id }}" selected>{{ $currentSemester->name }} (Current
                                Semester)</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="department_id">Department:</label>
                        <select name="department_id" id="department_id" class="form-control" >
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">
                                    {{ $department->code . ': ' . $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <i class="text-danger">{{ $message }}</i>
                    @enderror
                    </div>

                    <div id="courses-container" style="display: none;">
                        <h3>Courses:</h3>
                        <div id="course-list"></div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Assign</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#department_id').change(function() {
                var departmentId = $(this).val();
                if (departmentId) {
                    $.ajax({
                        url: "{{ route('admin.get-department-courses') }}",
                        method: 'GET',
                        data: {
                            department_id: departmentId,
                            semester_id: "{{ $currentSemester->id }}"
                        },
                        success: function(response) {
                            $('#course-list').empty();
                            $.each(response, function(index, course) {
                                var levels = course.course_assignments.map(function(
                                    ca) {
                                    return ca.level;
                                });

                                // Filter out duplicate levels
                                levels = [...new Set(levels)].join(', ');

                                $('#course-list').append(
                                    '<div class="form-check">' +
                                    '<input class="form-check-input" type="checkbox" name="course_ids[]" value="' +
                                    course.id + '" id="course-' + course.id + '">' +
                                    '<label class="form-check-label" for="course-' +
                                    course.id + '">' +
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
            });
        });
    </script>
@endsection
