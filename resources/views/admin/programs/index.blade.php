@extends('admin.layouts.admin')

@section('title', 'Programs')

@section('css')
    <style>
        .table-hover tbody tr:hover {
            cursor: pointer;
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
@endsection

@section('admin')
    @include('admin.alert')

    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Programs</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#programModal"
                    onclick="prepareProgramModal('create')">
                    <i class="fas fa-plus"></i> Create New Program
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    {{-- <table class="table " id="example">
                        <thead>
                            <tr>
                                <th>sn</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Schedule Type</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programs as $program)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->code }}</td>
                                    <td>{{ ucfirst($program->class_schedule_type) }}</td>
                                    <td>{{ $program->duration_value }} {{ ucfirst($program->duration_type) }}</td>
                                    <td>
                                        <span class="badge {{ $program->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $program->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm text-primary" data-bs-toggle="modal"
                                                data-bs-target="#programModal"
                                                onclick="prepareProgramModal('edit', {{ json_encode($program) }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.programs.destroy', $program) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this program?');">
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
                                    <td colspan="6" class="text-center">No programs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table> --}}

                    <table class="table " id="example">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>SN</th>
                                <th><i class="fas fa-file-signature me-1"></i>Name</th>
                                <th><i class="fas fa-code me-1"></i>Code</th>
                                <th><i class="fas fa-clock me-1"></i>Schedule Type</th>
                                <th><i class="fas fa-calendar me-1"></i>Duration</th>
                                <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programs as $program)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->code }}</td>
                                    <td><i
                                            class="fas fa-{{ $program->class_schedule_type === 'morning' ? 'sun' : ($program->class_schedule_type === 'evening' ? 'moon' : 'calendar-alt') }} me-1"></i>{{ ucfirst($program->class_schedule_type) }}
                                    </td>
                                    <td><i class="fas fa-hourglass-half me-1"></i>{{ $program->duration_value }}
                                        {{ ucfirst($program->duration_type) }}</td>
                                    <td>
                                        <span class="badge {{ $program->status ? 'bg-success' : 'bg-danger' }}">
                                            <i
                                                class="fas {{ $program->status ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                            {{ $program->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm text-primary" data-bs-toggle="modal"
                                                data-bs-target="#programModal"
                                                onclick="prepareProgramModal('edit', {{ json_encode($program) }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm text-danger"
                                                onclick="confirmDelete({{ $program->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="fas fa-folder-open me-1"></i>No programs found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Program Modal -->
        <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="programModalLabel">Create Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="programForm" action="{{ route('admin.programs.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Program Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Program Code</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="class_schedule_type" class="form-label">Class Schedule Type</label>
                                    <select class="form-select" id="class_schedule_type" name="class_schedule_type">
                                        <option value="morning">Morning</option>
                                        <option value="evening">Evening</option>
                                        <option value="weekend">Weekend</option>
                                        <option value="flexible" selected>Flexible</option>
                                        <option value="hybrid">Hybrid</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="duration_type" class="form-label">Duration Type</label>
                                    <select class="form-select" id="duration_type" name="duration_type">
                                        <option value="years" selected>Years</option>
                                        <option value="semesters">Semesters</option>
                                        <option value="months">Months</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="duration_value" class="form-label">Duration Value</label>
                                    <input type="number" class="form-control" id="duration_value" name="duration_value"
                                        value="4" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="attendance_requirement" class="form-label">Attendance Requirement
                                        (%)</label>
                                    <input type="number" class="form-control" id="attendance_requirement"
                                        name="attendance_requirement" value="75.00" step="0.01" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tuition_fee_multiplier" class="form-label">Tuition Fee Multiplier</label>
                                    <input type="number" class="form-control" id="tuition_fee_multiplier"
                                        name="tuition_fee_multiplier" value="1.00" step="0.01" required>
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="status" name="status"
                                            value="1" checked>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Create Program</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        function prepareProgramModal(action, program = null) {
            const modal = document.getElementById('programModal');
            const form = document.getElementById('programForm');
            const modalTitle = document.getElementById('programModalLabel');
            const submitBtn = document.getElementById('submitBtn');
            const formMethod = document.getElementById('formMethod');

            // Reset form
            form.reset();

            if (action === 'create') {
                modalTitle.textContent = 'Create Program';
                submitBtn.textContent = 'Create Program';
                form.action = "{{ route('admin.programs.store') }}";
                formMethod.value = 'POST';
            } else if (action === 'edit' && program) {
                modalTitle.textContent = 'Edit Program';
                submitBtn.textContent = 'Update Program';
                form.action = `/admin/programs/${program.id}`;
                formMethod.value = 'PUT';

                // Populate form with existing program data
                document.getElementById('name').value = program.name;
                document.getElementById('code').value = program.code;
                document.getElementById('description').value = program.description || '';
                document.getElementById('class_schedule_type').value = program.class_schedule_type;
                document.getElementById('duration_type').value = program.duration_type;
                document.getElementById('duration_value').value = program.duration_value;
                document.getElementById('attendance_requirement').value = program.attendance_requirement;
                document.getElementById('tuition_fee_multiplier').value = program.tuition_fee_multiplier;
                document.getElementById('status').checked = program.status;
            }
        }

        // Optional: Handle form submission via AJAX if you want to avoid page reload
        // document.getElementById('programForm').addEventListener('submit', function(e) {
        //     // Uncomment and customize if you want AJAX submission
        //     // e.preventDefault();
        //     // const formData = new FormData(this);
        //     // fetch(this.action, {
        //     //     method: this.method,
        //     //     body: formData
        //     // })
        //     // .then(response => response.json())
        //     // .then(data => {
        //     //     // Handle success/error
        //     // });
        // });

        function confirmDelete(programId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This program will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, delete it!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/programs/${programId}`;
                    form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Optional: Add success message after form submission
        document.getElementById('programForm').addEventListener('submit', function(e) {
            const formMethod = document.getElementById('formMethod').value;
            const isCreate = formMethod === 'POST';

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
            submitBtn.disabled = true;

            // Add loading state handling if you implement AJAX submission
        });
    </script>
@endsection
