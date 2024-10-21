@extends('admin.layouts.admin')

@section('title', 'Assign Lecturer Department')
@section('css')
    <style>
        .form-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .form-title {
            color: #2d3436;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f2f6;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #636e72;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 2px solid #f1f2f6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #10ac84;
            box-shadow: 0 0 0 0.2rem rgba(16, 172, 132, 0.15);
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #f1f2f6;
            border-radius: 8px;
            height: 48px;
            padding: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: #2d3436;
        }

        .error-message {
            color: #ee5253;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .current-session-badge {
            background-color: #10ac84;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin-left: 0.5rem;
        }

        .courses-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .courses-title {
            color: #2d3436;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .submit-btn {
            background-color: #10ac84;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #0a8967;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Custom checkbox styles for course selection */
        .course-checkbox {
            display: none;
        }

        .course-label {
            display: block;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border: 2px solid #f1f2f6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .course-label:hover {
            background-color: #f8f9fa;
        }

        .course-checkbox:checked + .course-label {
            border-color: #10ac84;
            background-color: rgba(16, 172, 132, 0.1);
        }

        .course-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-code {
            font-weight: 600;
            color: #2d3436;
        }

        .course-title {
            color: #636e72;
            font-size: 0.9rem;
        }
    </style>
@endsection

@section('admin')
    @include('admin.return_btn')

    <div class="container py-4">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <form action="{{ route('admin.teacher.assignment.store') }}" method="POST">
                        @csrf

                        @if ($teacher !== null)
                            <h2 class="form-title">
                                <i class="fas fa-user-plus me-2"></i>
                                Assign Department and Courses to
                                {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}
                            </h2>
                        @else
                            <h2 class="form-title">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                New Course Assignment
                            </h2>

                            <div class="form-section">
                                <label for="teacher_id" class="form-label">Lecturer</label>
                                <select name="teacher_id" id="teacher_id" class="form-control single-select">
                                    <option value="">Select a lecturer</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->user->id }}">
                                            {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endif

                        <div class="form-section">
                            <label for="academic_session_id" class="form-label">Academic Session</label>
                            <select name="academic_session_id" id="academic_session_id" class="form-control single-select">
                                <option value="{{ $currentAcademicSession->id }}" selected>
                                    {{ $currentAcademicSession->name }}
                                    <span class="current-session-badge">Current Session</span>
                                </option>
                            </select>
                        </div>

                        <div class="form-section">
                            <label for="semester_id" class="form-label">Semester</label>
                            <select name="semester_id" id="semester_id" class="form-control single-select">
                                <option value="{{ $currentSemester->id }}" selected>
                                    {{ $currentSemester->name }}
                                    <span class="current-session-badge">Current Semester</span>
                                </option>
                            </select>
                        </div>

                        <div class="form-section">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-control single-select">
                                <option value="">Select a department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->code . ': ' . $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div id="courses-container" class="courses-section" style="display: none;">
                            <h3 class="courses-title">
                                <i class="fas fa-book me-2"></i>
                                Available Courses
                            </h3>
                            <div id="course-list" class="course-list"></div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-check me-2"></i>
                                Complete Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(document).ready(function() {
                    // Initialize Select2
                    $('.single-select').select2({
                        theme: 'classic',
                        width: '100%'
                    });

                    // Handle department change
                    $('#department_id').change(function() {
                        const departmentId = $(this).val();
                        const semesterId = $('#semester_id').val();

                        if (departmentId) {
                            $.ajax({
                                url: `/admin/courses/${departmentId}/${semesterId}`,
                                method: 'GET',
                                success: function(response) {
                                    if (response.courses.length > 0) {
                                        let courseHtml = '';
                                        response.courses.forEach(course => {
                                            courseHtml += `
                                            <div class="course-item">
                                                <input type="checkbox"
                                                       id="course_${course.id}"
                                                       name="courses[]"
                                                       value="${course.id}"
                                                       class="course-checkbox">
                                                <label for="course_${course.id}" class="course-label">
                                                    <div class="course-info">
                                                        <div>
                                                            <span class="course-code">${course.code}</span>
                                                            <span class="course-title">${course.title}</span>
                                                        </div>
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    </div>
                                                </label>
                                            </div>
                                        `;
                                        });
                                        $('#course-list').html(courseHtml);
                                        $('#courses-container').slideDown();
                                    } else {
                                        $('#course-list').html(
                                            '<p class="text-muted">No courses available for this department and semester.</p>'
                                        );
                                        $('#courses-container').slideDown();
                                    }
                                },
                                error: function() {
                                    $('#course-list').html(
                                        '<p class="text-danger">Error loading courses. Please try again.</p>'
                                    );
                                    $('#courses-container').slideDown();
                                }
                            });
                        } else {
                            $('#courses-container').slideUp();
                        }
                    });
                });
            </script>
        @endpush
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
