@extends('admin.layouts.admin')

@section('title', 'Manage Departments')

@section('admin')
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0">Departments List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Faculty</th>
                                        <th>Duration (years)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as $department)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $department->name }}</td>
                                            <td>{{ $department->faculty->name }}</td>
                                            <td>{{ $department->duration }} years</td>
                                            <td>
                                                <div class="d-flex justify-content-end">
                                                    <a class="me-2 text-secondary btn btn-sm"
                                                        href="{{ route('admin.department.show', $department) }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <a class="me-2 text-info btn btn-sm"
                                                        href="{{ route('admin.department.teacherCourses', $department) }}">
                                                        <i class="fas fa-book"></i>
                                                    </a>
                                                    <a class="me-2 text-success btn btn-sm"
                                                        href="{{ route('admin.department.departmentStudent', $department) }}">
                                                        <i class="fas fa-user-friends"></i>
                                                    </a>

                                                    <a href="{{ route('admin.department.edit', $department) }}"
                                                        class="btn btn-sm text-primary me-2"><i class="fas fa-edit"></i></a>
                                                    <form style="display: inline-block"
                                                        action="{{ route('admin.department.delete', $department) }}"
                                                        method="post"
                                                        onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm text-danger"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No departments found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0">{{ empty($departmentSingle) ? 'Create Department' : 'Update Department' }}</h4>
                    </div>
                    <div class="card-body">
                        <form class="row g-3" method="POST"
                            action="{{ !isset($departmentSingle) ? route('admin.department.store') : route('admin.department.update', $departmentSingle->id) }}">
                            @csrf

                            @isset($departmentSingle)
                                @method('PUT')
                            @endisset

                            <div class="mb-3">
                                <label for="name" class="form-label">Department Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', !empty($departmentSingle) ? $departmentSingle->name : '') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="duration" class="form-label">Total number of academic levels to graduate</label>
                                <input type="number" class="form-control" id="duration" name="duration"
                                    value="{{ old('duration', !empty($departmentSingle) ? $departmentSingle->duration : '') }}">
                                @error('duration')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description">{{ old('description', !empty($departmentSingle) ? $departmentSingle->description : '') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="faculty_id" class="form-label">Select Depending Faculty</label>
                                <select id="faculty_id" name="faculty_id" class="form-select">
                                    <option selected disabled>Choose faculty...</option>
                                    @foreach ($faculties as $faculty)
                                        <option value="{{ $faculty->id }}"
                                            {{ old('faculty_id', !empty($departmentSingle) && $departmentSingle->faculty_id == $faculty->id ? 'selected' : '') }}>
                                            {{ Str::title($faculty->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-secondary"> <i class="fas fa-save"></i>
                                    {{ isset($departmentSingle) ? 'Update' : 'Create' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
