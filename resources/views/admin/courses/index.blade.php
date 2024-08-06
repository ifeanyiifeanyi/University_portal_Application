@extends('admin.layouts.admin')

@section('title', 'Manage Courses')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div>
            <button class="btn btn-primary" id="addCourseBtn">Create Course</button>
        </div>
        <hr />
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>sn</th>
                                <th>Code</th>
                                <th>Title</th>
                                <th style="width: 20px !important">Credit Hours</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->credit_hours }}</td>
                                    <td>{{ formatDateWithOrdinal($course->created_at) }}</td>
                                    <!-- Using the helper function -->
                                    <td>
                                        <button class="btn btn-sm btn-info editCourseBtn" data-id="{{ $course->id }}"
                                            data-code="{{ $course->code }}" data-title="{{ $course->title }}"
                                            data-description="{{ $course->description }}"
                                            data-credit_hours="{{ $course->credit_hours }}">
                                            <i class="fadeIn animated bx bx-edit-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary viewCourseBtn" data-id="{{ $course->id }}"
                                            data-code="{{ $course->code }}" data-title="{{ $course->title }}"
                                            data-description="{{ $course->description }}"
                                            data-credit_hours="{{ $course->credit_hours }}">
                                            <i class="fadeIn animated bx bx-detail"></i>
                                        </button>

                                        <a onclick="return confirm('Are you sure ?')"  href="{{ route('admin.courses.delete', $course->id) }}" class="btn btn-sm btn-danger">
                                            <i class="fadeIn animated bx bx-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('admin.courses.modal')
        @include('admin.courses.details')

    </div>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            // show modal to create course
            $('#addCourseBtn').click(function() {
                $('#courseModalLabel').text('Add Course');
                $('#courseForm').trigger('reset');
                $('#course_id').val('');
                $('.text-danger').text('');
                $('#courseModal').modal('show');
            });

            // show modal to edit course
            $('.editCourseBtn').click(function() {
                $('#courseModalLabel').text('Edit Course');
                $('#course_id').val($(this).data('id'));
                $('#code').val($(this).data('code'));
                $('#title').val($(this).data('title'));
                $('#description').val($(this).data('description'));
                $('#credit_hours').val($(this).data('credit_hours'));
                $('.text-danger').text('');
                $('#courseModal').modal('show');
            });

            $('.viewCourseBtn').click(function() {

                // Setting the modal title and content
                $('#courseModalLabel').text('View Course Details');
                $('#modal_code').text($(this).data('code'));
                $('#modal_title').text($(this).data('title'));
                $('#modal_description').text($(this).data('description'));
                $('#modal_credit_hours').text($(this).data('credit_hours'));

                // Showing the modal
                $('#courseView').modal('show');
            });


            $('#courseForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let courseId = $('#course_id').val();
                // check url for update and create course
                let url = courseId ? '/admin/courses/update/' + courseId : '/admin/courses/store';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#courseModal').modal('hide');
                        toastr[response.notification['alert-type']](response.notification[
                            'message']);
                        location.reload(); // Refresh the page to see the changes

                    },
                    error: function(response) {
                        let errors = response.responseJSON.errors;
                        $('#codeError').text(errors.code ? errors.code[0] : '');
                        $('#titleError').text(errors.title ? errors.title[0] : '');
                        $('#descriptionError').text(errors.description ? errors.description[0] :
                            '');
                        $('#creditHoursError').text(errors.credit_hours ? errors.credit_hours[
                            0] : '');
                    }
                });
            });
        });
    </script>

@endsection
