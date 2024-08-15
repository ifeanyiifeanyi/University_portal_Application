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
                                <p> <a href="{{ route('admin.student.create') }}" class="btn btn-primary float-left"
                                        style="text-align: right">Create New
                                        Account</a>
                                </p>
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
                                                                    <i class="bx bx-book-add me-0"></i> Registered Courses History
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
