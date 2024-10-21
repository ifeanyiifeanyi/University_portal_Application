@extends('admin.layouts.admin')

@section('title', 'Assigned Lecturers')
@section('css')
    <style>
        a {
            text-decoration: none !important;
        }

        .assignment-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .assignment-card:hover {
            transform: translateY(-2px);
        }

        .assignment-header {
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }

        .assignment-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.875rem;
        }

        .info-value {
            color: #2d3748;
        }

        .button-container {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-unassign {
            background-color: #dc3545;
            border: none;
            color: white;
        }

        .btn-edit {
            background-color: #4a5568;
            border: none;
            color: white;
        }

        .btn-view {
            background-color: #2b6cb0;
            border: none;
            color: white;
        }

        /* Accordion Styling */
        .custom-accordion {
            border-radius: 8px;
            overflow: hidden;
        }

        .accordion-header {
            background-color: #f8fafc;
            border: none;
        }

        .accordion-button {
            background-color: #f8fafc;
            color: #2d3748;
            font-weight: 600;
        }

        .accordion-button:not(.collapsed) {
            background-color: #edf2f7;
            color: #2d3748;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: #e2e8f0;
        }

        .department-list {
            list-style: none;
            padding-left: 0;
        }

        .department-list li {
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .department-list li:last-child {
            border-bottom: none;
        }

        .course-list {
            list-style: none;
            padding-left: 1.5rem;
            margin-top: 0.5rem;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }
    </style>
@endsection

@section('admin')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="m-0">@yield('title')</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.teacher.assignment.create') }}" class="btn btn-dark">
                    <i class="fas fa-plus-circle me-2"></i>Assign New Course
                </a>
                <button class="btn btn-secondary" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </button>
            </div>
        </div>

        <div id="message" role="alert"></div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Assignments Overview</h5>
                    </div>
                    <div class="card-body">
                        @if ($assignments->count() > 0)
                            @foreach ($assignments as $assignment)
                                <div class="assignment-card" data-id="{{ $assignment->id }}">
                                    <div class="assignment-header">
                                        <h5 class="mb-1">{{ $assignment->department->name }}</h5>
                                        <p class="text-muted mb-0">
                                            {{ $assignment->teacher->teacher_title . ' ' . $assignment->teacher->user->fullName() }}
                                        </p>
                                    </div>

                                    <div class="assignment-info">
                                        <div class="info-item">
                                            <span class="info-label">Course</span>
                                            <span class="info-value">{{ $assignment->course->code }} -
                                                {{ $assignment->course->title }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Academic Session</span>
                                            <span class="info-value">{{ $assignment->academicSession->name }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Semester</span>
                                            <span class="info-value">{{ $assignment->semester->name }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Assigned On</span>
                                            <span
                                                class="info-value">{{ \Carbon\Carbon::parse($assignment->created_at)->format('jS F Y') }}</span>
                                        </div>
                                    </div>

                                    <div class="button-container">
                                        <button class="btn btn-unassign" onclick="confirmUnassign({{ $assignment->id }})">
                                            <i class="fas fa-trash-alt me-2"></i>Unassign
                                        </button>
                                        <a href="{{ route('admin.teacher.assignment.edit', $assignment->id) }}"
                                            class="btn btn-edit">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                        <a href="{{ route('admin.teacher.assignment.show', $assignment->id) }}"
                                            class="btn btn-view">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No assignments available.</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white">
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Departments Overview</h5>
                    </div>
                    <div class="card-body">
                        @if ($departments->count() > 0)
                            <div class="custom-accordion accordion" id="departmentsAccordion">
                                @foreach ($departments as $department)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#dept{{ $department->id }}">
                                                <i class="fas fa-building me-2"></i>
                                                {{ $department->code }}: {{ $department->name }}
                                            </button>
                                        </h2>
                                        <div id="dept{{ $department->id }}" class="accordion-collapse collapse"
                                            data-bs-parent="#departmentsAccordion">
                                            <div class="accordion-body">
                                                <ul class="department-list">
                                                    @foreach ($department->teachers->unique('id') as $teacher)
                                                        @if ($teacher->teacherAssignments->where('department_id', $department->id)->count() > 0)
                                                            <li>
                                                                <div class="fw-bold">
                                                                    <i class="fas fa-user-tie me-2"></i>
                                                                    {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}
                                                                </div>
                                                                <ul class="course-list">
                                                                    @foreach ($teacher->teacherAssignments->where('department_id', $department->id) as $assignment)
                                                                        <li class="course-item">
                                                                            <span>{{ $assignment->course->code }} -
                                                                                {{ $assignment->course->title }}</span>
                                                                            <a href="{{ route('admin.teacher.assignment.show', $assignment->id) }}"
                                                                                class="btn btn-sm btn-outline-dark">
                                                                                Details
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No departments with active assignments available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @section('javascript')
    <script>
        function confirmUnassign(assignmentId) {
            if (confirm('Are you sure you want to unassign this course?')) {
                $.ajax({
                    url: "{{ route('admin.teacher.assignment.delete', '') }}/" + assignmentId,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            const messageDiv = $('#message');
                            messageDiv
                                .removeClass()
                                .addClass('alert alert-success')
                                .text(response.message)
                                .fadeIn();

                            // Fade out the assignment card
                            $(`.assignment-card[data-id="${assignmentId}"]`).fadeOut(400, function() {
                                $(this).remove();

                                // Check if there are any assignments left
                                if ($('.assignment-card').length === 0) {
                                    $('#assignments-overview').html(
                                        '<div class="text-center py-4"><p class="text-muted mb-0">No assignments available.</p></div>'
                                    );
                                }
                            });

                            // Hide message after 3 seconds
                            setTimeout(() => {
                                messageDiv.fadeOut();
                            }, 3000);

                        } else {
                            $('#message')
                                .removeClass()
                                .addClass('alert alert-danger')
                                .text(response.message);
                        }
                    },
                    error: function() {
                        $('#message')
                            .removeClass()
                            .addClass('alert alert-danger')
                            .text('An error occurred while unassigning the course.');
                    }
                });
            }
        }
    </script>
@endsection --}}
@section('javascript')
    <!-- Include SweetAlert2 CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>

    <script>
        function confirmUnassign(assignmentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will unassign the course from the lecturer. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, unassign it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing...',
                        html: 'Please wait while we unassign the course.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Perform the AJAX request
                    $.ajax({
                        url: "{{ route('admin.teacher.assignment.delete', '') }}/" + assignmentId,
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                // Fade out the assignment card
                                $(`.assignment-card[data-id="${assignmentId}"]`).fadeOut(400, function() {
                                    $(this).remove();

                                    // Check if there are any assignments left
                                    if ($('.assignment-card').length === 0) {
                                        $('#assignments-overview').html(
                                            '<div class="text-center py-4"><p class="text-muted mb-0">No assignments available.</p></div>'
                                        );
                                    }

                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Unassigned!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                });
                            } else {
                                // Show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while unassigning the course.',
                                footer: xhr.status === 404 ? 'The assignment was not found.' : 'Please try again later.'
                            });
                        }
                    });
                }
            });
        }

        // Initialize tooltips if you're using them
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
