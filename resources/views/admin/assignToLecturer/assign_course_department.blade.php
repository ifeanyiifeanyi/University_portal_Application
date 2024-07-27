@extends('admin.layouts.admin')

@section('title', 'Assigned Lecturers')
@section('css')
    <style>
        a {
            text-decoration: none !important;
        }

        .button-container {
            display: flex;
            gap: 10px;
        }
    </style>
@endsection

@section('admin')
    <div class="container">
        <h1>@yield('title')</h1>
        <a href="{{ route('admin.teacher.assignment.create') }}" class="btn btn-primary mb-3">Assign Content</a>
        <hr>

        <div id="message"></div> <!-- Place to display messages -->

        <div class="row">
            <div class="col-md-6">
                <h4 class="text-muted">Assignments Overview</h4>
                <div class="row" id="assignments-overview">
                    @if ($assignments->count() > 0)
                        @foreach ($assignments as $assignment)
                            <div class="card col-md-12 px-2 py-3 assignment-card" data-id="{{ $assignment->id }}">
                                <h3>{{ $assignment->department->name }}</h3>
                                <p><strong>Lecturer:</strong>
                                    {{ $assignment->teacher->teacher_title . ' ' . $assignment->teacher->user->fullName() }}
                                </p>
                                <p><strong>Course:</strong> {{ $assignment->course->code }} -
                                    {{ $assignment->course->title }}
                                </p>
                                <p><strong>Academic Session:</strong> {{ $assignment->academicSession->name }}</p>
                                <p><strong>Semester:</strong> {{ $assignment->semester->name }}</p>
                                <p><strong>Assigned On:</strong>
                                    {{ \Carbon\Carbon::parse($assignment->created_at)->format('jS F Y g:i A') }}</p>
                                <div class="button-container">
                                    <button style="background: linear-gradient(145deg, #9e0696, #ff4757)" type="button"
                                        class="btn btn-danger" onclick="confirmUnassign({{ $assignment->id }})">
                                        Unassign
                                    </button>

                                    <a style="background: linear-gradient(145deg, #40069e, #47f9ff)"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Assignment"
                                        href="{{ route('admin.teacher.assignment.edit', $assignment->id) }}"
                                        class="btn btn-info text-light">Edit</a>

                                    <a style="background: linear-gradient(145deg, #069e96, #47e3ff)"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="View Details"
                                        href="{{ route('admin.teacher.assignment.show', $assignment->id) }}"
                                        class="btn btn-primary">
                                        View Details
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No assignments available.</p>
                    @endif
                </div>
                <div class="d-flex justify-content-center">
                    {{ $assignments->links() }}
                </div>
            </div>

            <div class="col-md-6">
                <h4 class="text-muted">Departments and Assigned Lecturers</h4>
                @if($departments->count() > 0)
                    <div class="accordion" id="accordionExample">
                        @foreach ($departments as $department)
                            @php
                                $hasLecturers = false;
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $department->id }}">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $department->id }}" aria-expanded="true"
                                        aria-controls="collapse{{ $department->id }}">
                                        {{ $department->code }}: {{ $department->name }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $department->id }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading{{ $department->id }}" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <h4>Lecturers</h4>
                                        <ul>
                                            @foreach ($department->teachers->unique('id') as $teacher)
                                                @if ($teacher->teacherAssignments->where('department_id', $department->id)->count() > 0)
                                                    @php
                                                        $hasLecturers = true;
                                                    @endphp
                                                    <li>
                                                        {{ $teacher->teacher_title . ' ' . $teacher->user->fullName() }}
                                                        <ul>
                                                            @foreach ($teacher->teacherAssignments->where('department_id', $department->id) as $assignment)
                                                                <li>
                                                                    {{ $assignment->course->code }} - {{ $assignment->course->title }}
                                                                    <a href="{{ route('admin.teacher.assignment.show', $assignment->id) }}"
                                                                        class="btn btn-link">Details <i
                                                                            class="fas fa-angle-double-right"></i></a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        @if (!$hasLecturers)
                                            <p>No assignments available for this department.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (!$hasLecturers)
                                <script>
                                    document.querySelector('#heading{{ $department->id }}').parentElement.style.display = 'none';
                                </script>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No departments with active assignments available.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
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
                            $('#message').text(response.message).removeClass('alert-danger').addClass(
                                'alert alert-success');
                            setTimeout(function() {
                                $('.assignment-card[data-id="' + assignmentId + '"]').remove();
                                $('#message').removeClass('alert alert-success').text('');

                                // Check if there are any assignments left, if not show a message
                                if ($('#assignments-overview .assignment-card').length === 0) {
                                    $('#assignments-overview').html(
                                        '<p class="text-muted">No assignments available.</p>');
                                }

                                // Check if the department has any assignments left, if not hide the department
                                $('#accordionExample .accordion-item').each(function() {
                                    if ($(this).find('li').length === 0) {
                                        $(this).remove();
                                    }
                                });

                                // If no departments are left, show a message
                                if ($('#accordionExample .accordion-item').length === 0) {
                                    $('#accordionExample').html(
                                        '<p class="text-muted">No departments with active assignments available.</p>'
                                        );
                                }
                            }, 3000); // Delay the reload by 3 seconds
                        } else {
                            $('#message').text(response.message).removeClass('alert-success').addClass(
                                'alert alert-danger');
                        }
                    },
                    error: function(xhr) {
                        $('#message').text('An error occurred while unassigning the course.').removeClass(
                            'alert-success').addClass('alert alert-danger');
                    }
                });
            }
        }
    </script>
@endsection
