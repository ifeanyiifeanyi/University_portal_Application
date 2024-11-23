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
                                            <h3 class="mb-0">{{ $users->count() }}</h3>
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="departmentsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 rounded-start ps-4">#</th>
                                <th class="border-0">Department</th>
                                <th class="border-0">Faculty</th>
                                <th class="border-0">Duration</th>
                                <th class="border-0">Contact</th>
                                <th class="border-0 rounded-end text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                        <span class="badge bg-light text-dark">{{ $department->faculty->name }}</span>
                                    </td>
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

                                            <button class="btn btn-sm edit-department" data-id="{{ $department->id }}"
                                                data-bs-toggle="modal" data-bs-target="#departmentModal">
                                                <i class="fas fa-edit text-dark"></i>
                                            </button>

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
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this department?');">
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

    <!-- Department Modal -->
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
                                        placeholder="Department Name" required>
                                    <label>Department Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="Phone Number">
                                    <label>Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Email">
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
                                    <input type="number" class="form-control" id="duration" name="duration"
                                        min="1" max="8" placeholder="Duration" required>
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

            // Initialize edit buttons
            document.querySelectorAll('.edit-department').forEach(button => {
                button.addEventListener('click', (e) => this.handleEdit(e));
            });
        },

        resetForm() {
            this.formElement.reset();
            this.formElement.querySelector('#formMethod').value = 'POST';
            this.formElement.querySelector('#departmentId').value = '';
            document.getElementById('departmentModalLabel').textContent = 'Create Department';
            this.submitButton.textContent = 'Create Department';
        },

        async handleEdit(e) {
            const id = e.currentTarget.dataset.id;
            const loadingHtml =
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
            const modalBody = this.modalElement.querySelector('.modal-body');

            try {
                modalBody.innerHTML = loadingHtml;

                const response = await fetch(`/admin/manage-department/edit/${id}`);
                const data = await response.json();

                if (data.department) {
                    const dept = data.department;

                    // Update form method and ID
                    this.formElement.querySelector('#formMethod').value = 'PUT';
                    this.formElement.querySelector('#departmentId').value = dept.id;

                    // Update form fields
                    Object.keys(dept).forEach(key => {
                        const input = this.formElement.querySelector(`#${key}`);
                        if (input) {
                            input.value = dept[key];
                        }
                    });

                    // Update modal title and button
                    document.getElementById('departmentModalLabel').textContent = 'Edit Department';
                    this.submitButton.textContent = 'Update Department';

                    // Update form action
                    this.formElement.action = `/admin/manage-department/show/${id}`;
                }
            } catch (error) {
                console.error('Error fetching department data:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load department data'
                });
            }
        }
    };

    // View Modal Manager
    const DepartmentViewManager = {
        modalElement: null,
        contentElement: null,

        init() {
            this.modalElement = document.getElementById('departmentViewModal');
            this.contentElement = document.getElementById('departmentDetailsContent');

            // Initialize view buttons
            document.querySelectorAll('.view-department').forEach(button => {
                button.addEventListener('click', (e) => this.handleView(e));
            });
        },

        async handleView(e) {
            const id = e.currentTarget.dataset.id;
            const loadingHtml =
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

            try {
                this.contentElement.innerHTML = loadingHtml;

                const response = await fetch(`/admin/departments/${id}`);
                const data = await response.json();

                if (data.success && data.department) {
                    const dept = data.department;
                    this.renderDepartmentDetails(dept);
                } else {
                    this.renderError('Failed to load department details');
                }
            } catch (error) {
                console.error('Error fetching department details:', error);
                this.renderError('An error occurred while loading the department details');
            }
        },

        renderDepartmentDetails(dept) {
            this.contentElement.innerHTML = `
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-initial rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <span class="text-primary fw-bold">${dept.name ? dept.name.charAt(0) : 'N/A'}</span>
                        </div>
                        <div>
                            <h5 class="mb-1">${dept.name || 'N/A'}</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary">${dept.code || 'No Code'}</span>
                        </div>
                    </div>
                    <div class="vstack gap-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            <span>${dept.email || 'No email provided'}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <span>${dept.phone || 'No phone provided'}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock text-muted me-2"></i>
                            <span>${dept.duration || 'N/A'} years</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Department Details</h6>
                    <div class="vstack gap-3">
                        <div>
                            <small class="text-muted d-block mb-1">Faculty</small>
                            <span class="badge bg-light text-dark">${dept.faculty ? dept.faculty.name : 'Not Assigned'}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">Program</small>
                            <span class="badge bg-light text-dark">${dept.program ? dept.program.name : 'Not Assigned'}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block mb-1">Department Head</small>
                            <span>${dept.department_head && dept.department_head.teacher ?
                                dept.department_head.teacher.title_and_full_name : 'Not Assigned'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <h6 class="text-muted mb-3">Description</h6>
                    <p class="mb-0">${dept.description || 'No description available.'}</p>
                </div>

                <!-- Statistics Section -->
                <div class="col-12 mt-4">
                    <hr>
                    <h6 class="text-muted mb-3">Quick Statistics</h6>
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-users mb-2 text-primary"></i>
                                    <h5 class="mb-1">${dept.students_count || 0}</h5>
                                    <small class="text-muted">Students</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chalkboard-teacher mb-2 text-success"></i>
                                    <h5 class="mb-1">${dept.teachers_count || 0}</h5>
                                    <small class="text-muted">Teachers</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-book mb-2 text-info"></i>
                                    <h5 class="mb-1">${dept.courses_count || 0}</h5>
                                    <small class="text-muted">Courses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock mb-2 text-warning"></i>
                                    <h5 class="mb-1">${dept.duration || 0}</h5>
                                    <small class="text-muted">Years Duration</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        },

        renderError(message) {
            this.contentElement.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                <p class="mb-0">${message}</p>
            </div>
        `;
        }
    };

    // Initialize both managers when the document is ready
    document.addEventListener('DOMContentLoaded', () => {
        DepartmentModalManager.init();
        DepartmentViewManager.init();
    });
</script>
