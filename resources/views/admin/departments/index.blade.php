@extends('admin.layouts.admin')

@section('title', 'Manage Departments')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <a href="{{ route('admin.department.view') }}">
                            Back to Departments
                            <i class="bx bx-right-arrow-alt mb-4"></i>
                        </a>

                        <hr>
                        @if (isset($departmentSingle))
                            <div class="card-title d-flex align-items-center">
                                <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-primary">Update Department details</h5>

                            </div>
                            {{-- @dd($departmentSingle->duration) --}}
                            <form class="row g-3" method="POST"
                                action="{{ route('admin.department.update', $departmentSingle->id) }}">

                                @csrf
                                @method('PUT')

                                <div class="col-md-12 mb-3">
                                    <label for="first_name" class="form-label">Department Name</label>
                                    <input type="text" class="form-control" id="first_name" name="name"
                                        value="{{ old('name', $departmentSingle->name ?? '') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="duration" class="form-label">Total number of acaemic level to graduate</label>
                                    <input type="number" class="form-control" id="duration" name="duration"
                                        value="{{ old('duration', $departmentSingle->duration ?? '') }}">
                                    @error('duration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-m
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea type="password" class="form-control" id="description" name="description"">{{ old('name', $departmentSingle->description ?? '') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-5">
                                    <label for="faculty_id" class="form-label">Select Depending
                                        <strong>Faculty</strong></label>
                                    <select id="faculty_id" name="faculty_id" class="form-select single-select">
                                        <option selected disabled>Choose faculty...</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}"
                                                {{ old('faculty_id', $departmentSingle->faculty_id ?? '') == $faculty->id ? 'selected' : '' }}>
                                                {{ Str::title($faculty->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-5 w-100">
                                        {{ isset($departmentSingle) ? 'Update' : 'Create' }}

                                    </button>
                                </div>
                            </form>
                        @else
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            <h5 class="mb-0 text-primary">Create New Department</h5>

                        </div>
                            <form class="row g-3" method="POST" action="{{ route('admin.department.store') }}">
                                @csrf

                                <div class="col-md-12 mb-3">
                                    <label for="inputFirstName" class="form-label">Department Name</label>
                                    <input type="text" class="form-control" id="inputFirstName" name="name"
                                        value="{{ old('name', $department->name ?? '') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="duration" class="form-label">Total number of acaemic level to graduate</label>
                                    <input type="number" class="form-control" id="level" name="duration"
                                        value="{{ old('duration', $department->duration ?? '') }}">
                                    @error('duration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea type="password" class="form-control" id="description" name="description"">{{ old('name', $department->description ?? '') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-5">
                                    <label for="faculty_id" class="form-label">Select Depending
                                        <strong>Faculty</strong></label>
                                    <select id="faculty_id" name="faculty_id" class="form-select single-select">
                                        <option selected disabled>Choose faculty...</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}"
                                                {{ old('faculty_id', $department->faculty_id ?? '') == $faculty->id ? 'selected' : '' }}>
                                                {{ Str::title($faculty->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-5 w-100">
                                        {{ isset($department) ? 'Update' : 'Create' }}

                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            <h5 class="mb-0 text-primary">Departments List</h5>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="example2" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Faculty</th>
                                        <th scope="col">Duration</th>
                                        <th scope="col">Date Created</th>
                                        <th scope="col">Last Update Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as  $department)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <th scope="row">{{ $department->name }}</th>
                                            <th scope="row">{{ $department->faculty->name }}</th>
                                            <th scope="row">{{ $department->duration }}</th>
                                            <th scope="row">
                                                {{ \Carbon\Carbon::parse($department->created_at)->format('jS F Y g:i A') }}
                                            </th>
                                            <th scope="row">
                                                {{ \Carbon\Carbon::parse($department->updated_at)->format('jS F Y g:i A') }}
                                            </th>
                                            <th scope="row">
                                                <div class="col">
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                                        <ul class="dropdown-menu custom-dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.department.edit', $department) }}">
                                                                    <i class="lni lni-cloud-upload"></i>
                                                                    Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.department.show', $department) }}">
                                                                    <i class="lni lni-notepad"></i>
                                                                    Details
                                                                </a>
                                                            </li>
                                                            <li><a class="dropdown-item" href="#"> -----------------
                                                                </a></li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('admin.department.delete', $department) }}"
                                                                    method="post" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button
                                                                        onclick="return confirm('Are you sure of this action ?')"
                                                                        class="bg-danger dropdown-item text-white" type="submit">
                                                                        <i class="fas fa-trash"></i>
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
    @endsection

    @section('javascript')

    @endsection
