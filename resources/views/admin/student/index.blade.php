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
                            <table id="example2" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Matr No.</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Year of Admission</th>
                                        <th scope="col">Current Level</th>
                                        <th scope="col">Mode of Entry</th>
                                        <th scope="col">cgpa</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $student)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <th>{{ $student->user->first_name . ' ' . $student->user->last_name }}</th>
                                            <th>{{ $student->matric_number }}</th>
                                            <th>{{ $student->department->name }}</th>
                                            <th>{{ $student->year_of_admission }}</th>
                                            <th>{{ $student->current_level }}</th>
                                            <th>{{ $student->mode_of_entry }}</th>
                                            <th>{{ $student->cgpa ?? 'loading' }}</th>
                                            <th scope="row">
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <span class=" dropdown-toggle" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">....</span>
                                                        <ul class="dropdown-menu custom-dropdown-menu"
                                                            style="text-align: justify">
                                                            <li><a class="dropdown-item" href="">
                                                                    <i class="bx bx-edit me-0"></i> Edit

                                                                </a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.student.details', $student) }}">
                                                                    <i class="bx bx-coin-stack me-0"></i> View Details

                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form action="" method="post" id="deleteStudent">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button id="deleteStudentButton"
                                                                        class="dropdown-item  bg-danger" type="submit">
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
        document.getElementById('deleteStudent').addEventListener('submit', function(event) {
            event.preventDefault();

            if (confirm('Are you sure you want to delete this teacher?')) {

                document.getElementById('deleteStudentButton').submit();
            }
        });
    </script>
@endsection
