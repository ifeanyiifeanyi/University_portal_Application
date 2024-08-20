@extends('admin.layouts.admin')

@section('title', 'Faculties Manager')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div>
            <button class="btn btn-primary" id="addFacultyBtn"><i class="fadeIn animated bx bx-add-to-queue"></i> Add
                Faculty</button>
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
                                <th>Name</th>
                                <th>Created </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($faculties as $faculty)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $faculty->code }}</td>
                                    <td>{{ $faculty->name }}</td>
                                    <td>{{ formatDateWithOrdinal($faculty->created_at) }}</td>
                                    <!-- Using the helper function -->
                                    <td class="d-flex justify-content-center">
                                        <button style="background: transparent" class="border-0 editFacultyBtn" data-id="{{ $faculty->id }}"
                                            data-code="{{ $faculty->code }}" data-name="{{ $faculty->name }}"
                                            data-description="{{ $faculty->description }}">
                                            <x-edit-icon />

                                        </button>
                                        <button style="background: transparent" class="border-0 viewFacutlyBtn" data-id="{{ $faculty->id }}"
                                            data-code="{{ $faculty->code }}" data-name="{{ $faculty->name }}"
                                            data-description="{{ $faculty->description }}"
                                            data-departments="{{ $faculty->departments->pluck('name') }}"
                                            data-faculty="{{ $faculty }}">
                                            <x-view-icon />
                                        </button>
                                        <form onsubmit="return confirm('Are you sure ?')"
                                            action="{{ route('faculty-manager.destroy', $faculty) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button style="background: transparent" type="submit" class="text-danger border-0">
                                                <x-delete-icon />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('admin.faculties.modal')
        @include('admin.faculties.detail')

    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // show modal to create faculty
            $('#addFacultyBtn').click(function() {
                $('#facultyModalLabel').text('Create new faculty');
                $('#facultyform').trigger('reset');
                $('#faculty_id').val('');
                $('.text-danger').text('');
                $('#facultyModal').modal('show');
            });

            // show modal to edit faculty
            $('.editFacultyBtn').click(function() {
                $('#facultyModalLabel').text('Edit Faculty');
                $('#faculty_id').val($(this).data('id'));
                $('#code').val($(this).data('code'));
                $('#name').val($(this).data('name'));
                $('#description').val($(this).data('description'));
                $('.text-danger').text('');
                $('#facultyModal').modal('show');
            });

            // show modal to view faculty details
            $('.viewFacutlyBtn').click(function() {
                $('#facultyModalLabel').text('View Faculty Details');
                $('#modal_code').text($(this).data('code'));
                $('#modal_name').text($(this).data('name'));
                $('#modal_description').text($(this).data('description'));

                // Handle departments
                let departments = $(this).data('departments');
                let departmentsList = $('#modal_departments');
                departmentsList.empty();
                if (departments && departments.length > 0) {
                    departments.forEach(function(dept) {
                        departmentsList.append('<li>' + dept + '</li>');
                    });
                } else {
                    departmentsList.append('<li>No departments assigned</li>');
                }

                $('#courseView').modal('show');
            });

            // submite & update for faculty
            $('#facultyform').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let facultyId = $('#faculty_id').val();
                // check url for update and create faculty
                let url = facultyId ? '/admin/faculty-manager/' + facultyId : '/admin/faculty-manager';

                $.ajax({
                    url: url,
                    type: facultyId ? 'PATCH' : 'POST',
                    data: formData,
                    success: function(response) {
                        $('#facultyModal').modal('hide');
                        toastr[response.notification['alert-type']](response.notification[
                            'message']);
                        location.reload(); // Refresh the page to see the changes

                    },
                    error: function(response) {
                        let errors = response.responseJSON.errors;
                        $('#codeError').text(errors.code ? errors.code[0] : '');
                        $('#nameError').text(errors.name ? errors.name[0] : '');
                        $('#descriptionError').text(errors.description ? errors.description[0] :
                            '');
                    }
                });
            });
        })
    </script>
@endsection
