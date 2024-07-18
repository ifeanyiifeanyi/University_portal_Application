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
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            <h5 class="mb-0 text-primary">Lecturers List</h5>
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
                                            <th scope="row">{{ $teacher->teacher_title }} {{ $teacher->user->fullName() }}</th>
                                            <th scope="row">{{ $teacher->date_of_employment }}</th>
                                            <th scope="row">{{ $teacher->employment_id }}</th>
                                            <th scope="row">{{ $teacher->teacher_type }}</th>
                                            <th scope="row">
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <span class=" dropdown-toggle" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">....</span>
                                                        <ul class="dropdown-menu custom-dropdown-menu">
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.teacher.edit', $teacher) }}">Edit</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.teacher.show', $teacher) }}">Details</a>
                                                            </li>
                                                            <li><a class="dropdown-item" href="#"> -----------------
                                                                </a></li>
                                                            <li>
                                                                <form
                                                                    action=""
                                                                    method="post">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button
                                                                        onclick="return preventDefault() confirm('Are you sure of this action ?')"
                                                                        class="dropdown-item text-danger" type="submit">
                                                                        Delete
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

@endsection
