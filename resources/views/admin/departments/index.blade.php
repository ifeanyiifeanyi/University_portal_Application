@extends('admin.layouts.admin')

@section('title', 'Manage Departments')

@section('admin')
    <div class="container-fluid py-5 bg-light">
        <!-- Department Overview -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-white rounded-3 shadow-sm p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

                        <button class="btn btn-sm btn-primary px-4 d-flex align-items-center gap-2" data-bs-toggle="modal"
                            data-bs-target="#departmentModal">
                            <i class="fas fa-plus"></i>
                            <span>Add</span>
                        </button>
                    </div>

                    <!-- Analytics Cards -->
                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle p-3 bg-primary bg-opacity-10">
                                            <i class="fas fa-building fa-2x text-light"></i>
                                        </div>
                                        <div class="ms-4">
                                            <p class="text-muted mb-0">Departments</p>
                                            <h3 class="mb-0">{{ $departments->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-6 col-xl-3">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle p-3 bg-success bg-opacity-10">
                                            <i class="fas fa-users fa-2x text-light"></i>
                                        </div>
                                        <div class="ms-4">
                                            <p class="text-muted mb-0">Faculty</p>
                                            <h3 class="mb-0">{{ $faculties->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle p-3 bg-info bg-opacity-10">
                                            <i class="fas fa-book fa-2x text-light"></i>
                                        </div>
                                        <div class="ms-4">
                                            <p class="text-muted mb-0">Programs</p>
                                            <h3 class="mb-0">{{ $programs->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 rounded-circle p-3 bg-warning bg-opacity-10">
                                            <i class="fas fa-user-tie fa-2x text-light"></i>
                                        </div>
                                        <div class="ms-4">
                                            <p class="text-muted mb-0">HOD</p>
                                            <h3 class="mb-0">
                                                {{ $departments->whereNotNull('department_head_id')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="example">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 rounded-start ps-4">#</th>
                                <th class="border-0">Department</th>
                                <th class="border-0">Faculty</th>
                                <th class="border-0">Program</th>
                                <th class="border-0">Duration</th>
                                <th class="border-0">Contact</th>
                                <th class="border-0 rounded-end text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($departments) --}}
                            @forelse ($departments as $department)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initial rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                                <span
                                                    class="text-light fw-bold">{{ substr($department->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-muted">{{ $department->name }}</p>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-light">{{ $department->code }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-light">{{ $department->faculty->name }}</span>
                                    </td>
                                    <td>{{ $department->program->name }}</td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-light">
                                            {{ $department->duration }} years
                                        </span>
                                    </td>
                                    <td>
                                        <div class="vstack gap-1">
                                            <div class="d-flex align-items-center text-muted mb-2">
                                                <i class="fas fa-envelope me-2"></i>
                                                <small>{{ $department->email ?? 'N/A' }}</small>
                                            </div>
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="fas fa-phone me-2"></i>
                                                <small>{{ $department->phone ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">

                                            <a href="{{ route('admin.department.show', $department->id) }}"
                                                class="btn btn-sm">
                                                <i class="fas fa-eye text-dark"></i>
                                            </a>

                                            <a href="{{ route('admin.department.edit', $department->id) }}"
                                                class="btn btn-sm">
                                                <i class="fas fa-edit text-dark"></i>
                                            </a>

                                            <div class="dropdown">
                                                <button class="btn btn-sm border-0" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v text-dark"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item py-2 px-4"
                                                            href="{{ route('admin.department.teacherCourses', $department->id) }}">
                                                            <i class="fas fa-book me-2"></i> Department Courses
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item py-2 px-4"
                                                            href="{{ route('admin.department.departmentStudent', $department->id) }}">
                                                            <i class="fas fa-users me-2"></i> Department Students
                                                        </a>
                                                    </li>


                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item py-2 px-4"
                                                            href="{{ route('admin.department.export-csv', $department->id) }}">
                                                            <i class="fas fa-file-export me-2"></i> Export Courses
                                                        </a>
                                                    </li>


                                                    <li>
                                                        <a class="dropdown-item py-2 px-4"
                                                            href="{{ route('admin.department.exportCsv', $department->id) }}">
                                                            <i class="fas fa-file-export me-2"></i> Export Students
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form class="d-inline"
                                                            action="{{ route('admin.department.delete', $department) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item py-2 px-4 text-danger">
                                                                <i class="fas fa-trash me-2"></i> Delete Department
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-folder-open text-muted fa-3x"></i>
                                            </div>
                                            <h5 class="text-muted mb-3">No Departments Found</h5>
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#departmentModal">
                                                Create Your First Department
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Department Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold" id="departmentModalLabel">Create Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="departmentForm" method="POST" action="{{ route('admin.department.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="department_id" id="departmentId">
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="" required>
                                    <label>Department Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="">
                                    <label>Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="">
                                    <label>Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="faculty_id" name="faculty_id" class="form-select" required>
                                        <option value="">Select Faculty</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                    <label>Faculty</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="level_format" name="level_format" class="form-select"
                                        onchange="handleLevelFormatChange()">
                                        <option value="">Standard Levels (100, 200, etc)</option>
                                        <option value="nd_hnd">ND/HND Format</option>
                                        <option value="nursing">Nursing Format (NS1-NS3)</option>
                                    </select>
                                    <label>Level Format</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="durationField">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="duration" name="duration"
                                        min="1" max="8" placeholder="" required>
                                    <label>Program Duration (Years)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="program_id" name="program_id" class="form-select">
                                        <option value="">Select Program (Optional)</option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                                        @endforeach
                                    </select>
                                    <label>Program</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <select id="department_head_id" name="department_head_id" class="form-select">
                                        <option value="">Select Department Head (Optional)</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->teacher->title_and_full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>Department Head</label>
                                </div>

                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="description" name="description" rows="3" style="height: 100px"
                                        placeholder="Description"></textarea>
                                    <label>Description</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" id="submitDepartment">Create
                            Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Custom CSS -->
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .avatar-initial {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-floating>.form-control,
        .form-floating>.form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }

        .form-floating>label {
            padding: 1rem 0.75rem;
        }

        .dropdown-item {
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            padding-left: 1.75rem !important;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
        }
    </style>
@endsection




@section('javascript')
    <script>
        function handleLevelFormatChange() {
            const levelFormat = document.getElementById('level_format').value;
            const durationField = document.getElementById('durationField');
            const durationInput = document.getElementById('duration');

            switch (levelFormat) {
                case 'nd_hnd':
                    durationInput.value = 4;
                    durationInput.readOnly = true;
                    durationField.style.display = 'block';
                    break;
                case 'nursing':
                    durationInput.value = 3;
                    durationInput.readOnly = true;
                    durationField.style.display = 'block';
                    break;
                default:
                    durationInput.value = '';
                    durationInput.readOnly = false;
                    durationField.style.display = 'block';
            }
        }


        // Add this to your DepartmentModalManager.resetForm method
        DepartmentModalManager.resetForm = function() {
            this.formElement.reset();
            this.formElement.querySelector('#formMethod').value = 'POST';
            this.formElement.querySelector('#departmentId').value = '';
            document.getElementById('departmentModalLabel').textContent = 'Create Department';
            this.submitButton.textContent = 'Create Department';
            document.getElementById('duration').readOnly = false;
            handleLevelFormatChange();
        }
    </script>
    <script>
        // Modal State Management
        const DepartmentModalManager = {
            modalElement: null,
            formElement: null,
            submitButton: null,

            init() {
                this.modalElement = document.getElementById('departmentModal');
                this.formElement = document.getElementById('departmentForm');
                this.submitButton = document.getElementById('submitDepartment');

                // Reset form on modal close
                this.modalElement.addEventListener('hidden.bs.modal', () => {
                    this.resetForm();
                });

                //
            },

            resetForm() {
                this.formElement.reset();
                this.formElement.querySelector('#formMethod').value = 'POST';
                this.formElement.querySelector('#departmentId').value = '';
                document.getElementById('departmentModalLabel').textContent = 'Create Department';
                this.submitButton.textContent = 'Create Department';
            },

        };

        const DepartmentDeleteHandler = {
            init() {
                // Find all department delete forms
                const deleteForms = document.querySelectorAll('form[action*="/admin/department/delete/"]');

                deleteForms.forEach(form => {
                    form.onsubmit = async (e) => {
                        e.preventDefault();

                        // Show SweetAlert confirmation
                        const result = await Swal.fire({
                            title: 'Delete Department?',
                            text: 'This action cannot be undone. Are you sure you want to delete this department?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true,
                            focusCancel: true,
                            customClass: {
                                confirmButton: 'btn btn-danger',
                                cancelButton: 'btn btn-secondary'
                            },
                        });

                        // If confirmed, submit the form
                        if (result.isConfirmed) {
                            try {
                                await form.submit();

                                // Show success message (optional, if you want to handle the response)
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The department has been deleted successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            } catch (error) {
                                // Show error message if something goes wrong
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong while deleting the department.',
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        }
                    };
                });
            }
        };



        // Initialize both managers when the document is ready
        document.addEventListener('DOMContentLoaded', () => {
            DepartmentModalManager.init();
            DepartmentDeleteHandler.init();
        });
    </script>
@endsection
