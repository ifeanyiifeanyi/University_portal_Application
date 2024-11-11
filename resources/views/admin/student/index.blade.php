@extends('admin.layouts.admin')

@section('title', 'Students Manager')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">


                        <div class="card-title d-flex align-items-center gap-5">
                            <div>
                                <a href="{{ route('admin.student.create') }}"
                                    class="btn btn-sm bg-secondary text-white float-right" style="text-align: right"><i
                                        class="fas fa-user-plus"></i> Create New
                                    Account</a>
                                <!-- Download Template Buttons -->
                                <div class="dropdown d-inline">
                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-download"></i> Download Template
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.students.template.download', ['format' => 'excel']) }}">
                                                <i class="fas fa-file-excel"></i> Excel Format
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.students.template.download', ['format' => 'pdf']) }}">
                                                <i class="fas fa-file-pdf"></i> PDF Format
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                {{-- <a href="{{ route('admin.students.import') }}"
                                    style="background-color: rgba(172, 255, 47, 0.616)" class="btn btn-sm float-left ms-2">
                                    <i class="fas fa-users-cog"></i> Import Students
                                </a> --}}
                                <!-- Import Button -->
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#importModal">
                                    <i class="fas fa-file-import"></i> Import Students
                                </button>

                                <!-- Import Modal -->
                                <div class="modal fade" id="importModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Import Students</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.students.import_verify') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Department</label>
                                                        <select name="department_id" class="form-select" required>
                                                            @foreach ($departments as $department)
                                                                <option value="{{ $department->id }}">
                                                                    {{ $department->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Excel File</label>
                                                        <input type="file" name="file" class="form-control"
                                                            accept=".xlsx,.xls" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Verify Data</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <hr>
                        <div class="table-responsive">
                            <table id="example" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Matr No.</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Year of Admission</th>
                                        <th scope="col">Current Level</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $student)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <th>

                                                {{ $student->user->fullName() ?? '' }}

                                            </th>
                                            <th>{{ $student->matric_number }}</th>
                                            <th>{{ $student->department->name }}</th>
                                            <th>{{ $student->year_of_admission }}</th>
                                            <th>{{ $student->current_level }}</th>
                                            <th scope="row">
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <span class=" dropdown-toggle text-primary" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <x-menu-icon />
                                                        </span>
                                                        <ul class="dropdown-menu custom-dropdown-menu"
                                                            style="text-align: justify">

                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.students.course-registrations', $student) }}">
                                                                    <i class="bx bx-book-add me-0"></i>
                                                                    View Course Registrations
                                                                </a>
                                                            </li>

                                                            <li class="dropdown-divider mb-0"> </li>

                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.assign.courseForStudent', $student) }}">
                                                                    <i class="bx bx-book-add me-0"></i> Register Courses
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider mb-0"> </li>

                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.students.registration-history', $student) }}">
                                                                    <i class="bx bx-book-add me-0"></i> Registered Courses
                                                                    History
                                                                </a>
                                                            </li>

                                                            <li class="dropdown-divider mb-0"> </li>


                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.student.edit', $student) }}">
                                                                    <i class="bx bx-edit me-0"></i> Edit

                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider mb-0"> </li>

                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.student.details', $student) }}">
                                                                    <i class="bx bx-coin-stack me-0"></i> View Details

                                                                </a>
                                                            </li>

                                                            <li class="dropdown-divider mb-2"> </li>

                                                            <li>
                                                                <form
                                                                    action="{{ route('admin.student.delete', $student) }}"
                                                                    method="post" class="delete-student-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="dropdown-item bg-danger text-light"
                                                                        type="submit">
                                                                        <i class="bx bx-trash-alt me-0"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script>
        document.addEventListener('click', function(event) {
            if (event.target.closest('.delete-student-form')) {
                event.preventDefault();

                if (confirm('Are you sure you want to delete this student?')) {
                    event.target.closest('.delete-student-form').submit();
                }
            }
        });
    </script>
@endsection
