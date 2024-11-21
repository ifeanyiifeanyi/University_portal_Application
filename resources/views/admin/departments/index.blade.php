@extends('admin.layouts.admin')

@section('title', 'Manage Departments')

@section('admin')
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Departments List</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="departmentsTable example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Faculty</th>

                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as $department)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $department->code }}</td>
                                            <td>{{ $department->name }}</td>
                                            <td>{{ $department->faculty->name }}</td>

                                            <td>{{ $department->duration }} years</td>
                                            <td>
                                                <div class="btn">
                                                    <button class="btn btn-sm text-info me-1 view-department"
                                                        data-id="{{ $department->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#departmentViewModal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm text-primary me-1 edit-department"
                                                        data-id="{{ $department->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#departmentModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form class="d-inline"
                                                        action="{{ route('admin.department.delete', $department) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm text-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No departments found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="departmentModalLabel">Create Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="departmentForm" method="POST" action="{{ route('admin.department.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="department_id" id="departmentId">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Department Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="faculty_id" class="form-label">Faculty</label>
                                <select id="faculty_id" name="faculty_id" class="form-select" required>
                                    <option value="">Select Faculty</option>
                                    @foreach ($faculties as $faculty)
                                        <option value="{{ $faculty->id }}">
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Program Duration (Years)</label>
                                <input type="number" class="form-control" id="duration" name="duration" min="1"
                                    max="8" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="program_id" class="form-label">Program</label>
                                <select id="program_id" name="program_id" class="form-select">
                                    <option value="">Select Program (Optional)</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id }}">
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department_head_id" class="form-label">Department Head</label>
                                <select id="department_head_id" name="department_head_id" class="form-select">
                                    <option value="">Select Department Head (Optional)</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->teacher->title_and_full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitDepartment">Create Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Department View Modal -->
    <div class="modal fade" id="departmentViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Department Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="departmentDetailsContent">
                    <!-- Details will be dynamically populated -->
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // $(document).ready(function() {
    //     // Edit Department
    //     $('.edit-department').on('click', function() {
    //         const departmentId = $(this).data('id');

    //         $.ajax({
    //             url: `/admin/departments/${departmentId}/edit`,
    //             method: 'GET',
    //             success: function(response) {
    //                 // Populate modal form with existing data
    //                 $('#departmentModalLabel').text('Update Department');
    //                 $('#submitDepartment').text('Update Department');
    //                 $('#departmentForm').attr('action',
    //                     `/admin/departments/${departmentId}`);
    //                 $('#formMethod').val('PUT');
    //                 $('#departmentId').val(departmentId);

    //                 // Populate fields
    //                 $('#code').val(response.code);
    //                 $('#name').val(response.name);
    //                 $('#phone').val(response.phone);
    //                 $('#email').val(response.email);
    //                 $('#faculty_id').val(response.faculty_id);
    //                 $('#duration').val(response.duration);
    //                 $('#program_id').val(response.program_id);
    //                 $('#department_head_id').val(response.department_head_id);
    //                 $('#description').val(response.description);
    //             }
    //         });
    //     });

    //     // View Department
    //     $('.view-department').on('click', function() {
    //         const departmentId = $(this).data('id');

    //         $.ajax({
    //             url: `/admin/departments/${departmentId}`,
    //             method: 'GET',
    //             success: function(response) {
    //                 let detailsHtml = `
    //                 <div class="row">
    //                     <div class="col-md-6 mb-2"><strong>Code:</strong> ${response.code}</div>
    //                     <div class="col-md-6 mb-2"><strong>Name:</strong> ${response.name}</div>
    //                     <div class="col-md-6 mb-2"><strong>Faculty:</strong> ${response.faculty.name}</div>
    //                     <div class="col-md-6 mb-2"><strong>Duration:</strong> ${response.duration} years</div>
    //                     <div class="col-md-6 mb-2"><strong>Phone:</strong> ${response.phone || 'N/A'}</div>
    //                     <div class="col-md-6 mb-2"><strong>Email:</strong> ${response.email || 'N/A'}</div>
    //                     <div class="col-md-6 mb-2"><strong>Program:</strong> ${response.program ? response.program.name : 'N/A'}</div>
    //                     <div class="col-md-6 mb-2"><strong>Department Head:</strong> ${response.department_head ? response.department_head.name : 'N/A'}</div>
    //                     <div class="col-12 mb-2">
    //                         <strong>Description:</strong>
    //                         <p>${response.description || 'No description available'}</p>
    //                     </div>
    //                 </div>
    //             `;
    //                 $('#departmentDetailsContent').html(detailsHtml);
    //             }
    //         });
    //     });
    // });

    $(document).ready(function() {
        // Department Form Submission
        $('#departmentForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const method = $('#formMethod').val();
            const url = method === 'PUT' ?
                `/admin/departments/${$('#departmentId').val()}` :
                '/admin/departments';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    // Show success toast or alert
                    toastr.success(response.message);

                    // Close the modal
                    $('#departmentModal').modal('hide');

                    // Reload the page or update the table dynamically
                    location.reload(); // Simple approach

                    // Alternatively, for a more dynamic approach:
                    // updateDepartmentTable(response.department);
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        // Clear previous error messages
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        // Display validation errors
                        $.each(errors, function(field, messages) {
                            const $input = $(`[name="${field}"]`);
                            $input.addClass('is-invalid');
                            $input.after(
                                `<div class="invalid-feedback">${messages[0]}</div>`
                            );
                        });

                        toastr.error('Please correct the errors in the form.');
                    } else {
                        // Handle other types of errors
                        toastr.error(xhr.responseJSON.message ||
                            'An unexpected error occurred');
                    }
                }
            });
        });

        // Optional: Dynamic table update function
        function updateDepartmentTable(department) {
            const $table = $('#departmentsTable tbody');

            // Check if department exists (update) or is new
            const $existingRow = $(`tr[data-id="${department.id}"]`);

            if ($existingRow.length) {
                // Update existing row
                $existingRow.html(`
                <td>${$existingRow.find('td:first').text()}</td>
                <td>${department.code}</td>
                <td>${department.name}</td>
                <td>${department.faculty.name}</td>
                <td>${department.phone || 'N/A'}</td>
                <td>${department.email || 'N/A'}</td>
                <td>${department.duration} years</td>
                <td>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm text-info me-1 view-department"
                            data-id="${department.id}"
                            data-bs-toggle="modal"
                            data-bs-target="#departmentViewModal">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm text-primary me-1 edit-department"
                            data-id="${department.id}"
                            data-bs-toggle="modal"
                            data-bs-target="#departmentModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form class="d-inline"
                            action="/admin/departments/${department.id}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this department?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            `);
            } else {
                // Add new row
                $table.append(`
                <tr data-id="${department.id}">
                    <td>${$table.find('tr').length + 1}</td>
                    <td>${department.code}</td>
                    <td>${department.name}</td>
                    <td>${department.faculty.name}</td>
                    <td>${department.phone || 'N/A'}</td>
                    <td>${department.email || 'N/A'}</td>
                    <td>${department.duration} years</td>
                    <td>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-sm text-info me-1 view-department"
                                data-id="${department.id}"
                                data-bs-toggle="modal"
                                data-bs-target="#departmentViewModal">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm text-primary me-1 edit-department"
                                data-id="${department.id}"
                                data-bs-toggle="modal"
                                data-bs-target="#departmentModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form class="d-inline"
                                action="/admin/departments/${department.id}"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this department?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `);
            }

            // Rebind event listeners
            bindDepartmentEvents();
        }

        // Reset modal when opened
        $('#departmentModal').on('show.bs.modal', function() {
            // Reset form
            $('#departmentForm')[0].reset();

            // Reset method and labels for create
            $('#departmentModalLabel').text('Create Department');
            $('#submitDepartment').text('Create Department');
            $('#departmentForm').attr('action', '{{ route('admin.department.store') }}');
            $('#formMethod').val('POST');
            $('#departmentId').val('');

            // Remove any existing validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        });

        // Bind event listeners (can be called after dynamic content updates)
        function bindDepartmentEvents() {
            // Existing edit and view department event listeners
            $('.edit-department').on('click', function() {
                const departmentId = $(this).data('id');

                $.ajax({
                    url: `/admin/departments/${departmentId}/edit`,
                    method: 'GET',
                    success: function(response) {
                        $('#departmentModalLabel').text('Update Department');
                        $('#submitDepartment').text('Update Department');
                        $('#departmentForm').attr('action',
                            `/admin/departments/${departmentId}`);
                        $('#formMethod').val('PUT');
                        $('#departmentId').val(departmentId);

                        // Populate fields
                        $('#code').val(response.code);
                        $('#name').val(response.name);
                        $('#phone').val(response.phone);
                        $('#email').val(response.email);
                        $('#faculty_id').val(response.faculty_id);
                        $('#duration').val(response.duration);
                        $('#program_id').val(response.program_id);
                        $('#department_head_id').val(response.department_head_id);
                        $('#description').val(response.description);
                    }
                });
            });

            // View department (same as before)
            $('.view-department').on('click', function() {
                const departmentId = $(this).data('id');

                $.ajax({
                    url: `/admin/departments/${departmentId}`,
                    method: 'GET',
                    success: function(response) {
                        let detailsHtml = `
                        <div class="row">
                            <div class="col-md-6 mb-2"><strong>Code:</strong> ${response.code}</div>
                            <div class="col-md-6 mb-2"><strong>Name:</strong> ${response.name}</div>
                            <div class="col-md-6 mb-2"><strong>Faculty:</strong> ${response.faculty.name}</div>
                            <div class="col-md-6 mb-2"><strong>Duration:</strong> ${response.duration} years</div>
                            <div class="col-md-6 mb-2"><strong>Phone:</strong> ${response.phone || 'N/A'}</div>
                            <div class="col-md-6 mb-2"><strong>Email:</strong> ${response.email || 'N/A'}</div>
                            <div class="col-md-6 mb-2"><strong>Program:</strong> ${response.program ? response.program.name : 'N/A'}</div>
                            <div class="col-md-6 mb-2"><strong>Department Head:</strong> ${response.department_head ? response.department_head.name : 'N/A'}</div>
                            <div class="col-12 mb-2">
                                <strong>Description:</strong>
                                <p>${response.description || 'No description available'}</p>
                            </div>
                        </div>
                    `;
                        $('#departmentDetailsContent').html(detailsHtml);
                    }
                });
            });
        }

        // Initial binding of events
        bindDepartmentEvents();
    });
</script>
