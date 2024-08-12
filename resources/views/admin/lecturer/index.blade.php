@extends('admin.layouts.admin')

@section('title', 'Mange Lecturers')
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
                                <p> <a href="{{ route('admin.teacher.create') }}" class="btn btn-primary float-left"
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
                                        <th scope="col">Employment Date</th>
                                        <th scope="col">Employment ID</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($teachers as  $teacher)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <th scope="row">{{ $teacher->teacher_title }}
                                                {{ $teacher->user->fullName() }}</th>
                                            <th scope="row">{{ $teacher->date_of_employment }}</th>
                                            <th scope="row">{{ $teacher->employment_id }}</th>
                                            <th scope="row">{{ $teacher->teacher_type }}</th>
                                            <th scope="row">
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <span class=" dropdown-toggle text-primary" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <x-menu-icon />
                                                        </span>
                                                        <ul class="dropdown-menu custom-dropdown-menu"
                                                            style="text-align: justify">
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.teacher.edit', $teacher) }}">
                                                                    <i class="bx bx-edit me-0"></i> Edit

                                                                </a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.teacher.show', $teacher) }}">
                                                                    <i class="bx bx-coin-stack me-0"></i> View Details

                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('admin.teachers.delete', $teacher) }}"
                                                                    method="post" id="deleteTeacher">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button id="deleteTeacherButton"
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
                                        <div class="alert alert-danger shadow">No Departments at the moment</div>
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
        document.getElementById('deleteTeacher').addEventListener('submit', function(event) {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this teacher?')) {
                document.getElementById('deleteTeacher').submit();
            }
        });
    </script>
@endsection
