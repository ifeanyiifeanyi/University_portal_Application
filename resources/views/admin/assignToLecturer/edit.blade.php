@extends('admin.layouts.admin')

@section('title', 'Edit Assignment')

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

        /* Specific styles for regular select boxes */
        .form-select {
            border: 2px solid #f1f2f6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            width: 100%;
            background-color: white;
        }

        .form-select:focus {
            border-color: #10ac84;
            box-shadow: 0 0 0 0.2rem rgba(16, 172, 132, 0.15);
            outline: none;
        }

        /* Select2 specific styling */
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

        .error-message {
            color: #ee5253;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .course-item {
            background: white;
            border: 2px solid #f1f2f6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .course-item:hover {
            border-color: #10ac84;
            background-color: rgba(16, 172, 132, 0.05);
        }

        .course-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .course-code {
            font-weight: 600;
            color: #2d3436;
            min-width: 100px;
        }

        .course-title {
            color: #636e72;
            flex: 1;
        }

        .course-level {
            color: #10ac84;
            font-size: 0.85rem;
            font-weight: 500;
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

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            background-color: #10ac84;
            color: white;
            display: inline-block;
            margin-left: 0.5rem;
        }
    </style>
@endsection

@section('admin')
    @include('admin.return_btn')

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <h2 class="form-title">
                        <i class="fas fa-edit me-2"></i>
                        Edit Course Assignment
                    </h2>

                    <form action="{{ route('admin.teacher.assignment.update', $assignment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-section">
                            <label for="teacher_id" class="form-label">Lecturer</label>
                            <select name="teacher_id" id="teacher_id" class="form-control select2-single">
                                <option value="">Select Lecturer</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->user->id }}"
                                        {{ $assignment->teacher->user->id == $teacher->user->id ? 'selected' : '' }}>
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

                        <div class="form-section">
                            <label for="academic_session_id" class="form-label">Academic Session</label>
                            <select name="academic_session_id" id="academic_session_id" class="form-select">
                                <option value="{{ $currentAcademicSession->id }}" selected>
                                    {{ $currentAcademicSession->name }}
                                    <span class="badge">Current Session</span>
                                </option>
                            </select>
                        </div>

                        <div class="form-section">
                            <label for="semester_id" class="form-label">Semester</label>
                            <select name="semester_id" id="semester_id" class="form-select">
                                <option value="{{ $currentSemester->id }}" selected>
                                    {{ $currentSemester->name }}
                                    <span class="badge">Current Semester</span>
                                </option>
                            </select>
                        </div>

                        <div class="form-section">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ $assignment->department_id == $department->id ? 'selected' : '' }}>
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

                        <div id="courses-container" class="courses-section"
                            style="display: {{ $assignment->department_id ? 'block' : 'none' }};">
                            <h3 class="courses-title">
                                <i class="fas fa-book me-2"></i>
                                Available Courses
                            </h3>
                            <div id="course-list">
                                {{-- Courses will be loaded dynamically --}}
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-save me-2"></i>
                                Update Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Initialize Select2 only for teacher selection
            $('#teacher_id').select2({
                theme: 'classic',
                width: '100%'
            });

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
                                levels = [...new Set(levels)].join(', ');

                                $('#course-list').append(`
                            <div class="course-item">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="course_ids[]"
                                           value="${course.id}"
                                           id="course-${course.id}"
                                           ${course.is_assigned ? 'checked' : ''}>
                                    <label class="form-check-label" for="course-${course.id}">
                                        <div class="course-info">
                                            <span class="course-code">${course.code}</span>
                                            <span class="course-title">${course.title}</span>
                                            <span class="course-level">Levels: ${levels}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        `);
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
