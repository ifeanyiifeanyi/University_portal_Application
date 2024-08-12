@extends('admin.layouts.admin')

@section('title', 'Manage Departments')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <form action="" method="post">
                        <div class="card-body p-5">
                            <div class="card-title d-flex align-items-center">
                                <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-primary">Create Departments</h5>
                            </div>
                            <hr>



                            @if (isset($departmentSingle))
                                <form class="row g-3" method="POST"
                                    action="{{ url('manage-department/update/'.$departmentSingle->id) }}">

                                    @csrf
                                    @method('PUT')

                                    <div class="col-md-12 mb-3">
                                        <label for="inputFirstName" class="form-label">Department Name</label>
                                        <input type="text" class="form-control" id="inputFirstName" name="name"
                                            value="{{ old('name', $departmentSingle->name ?? '') }}">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
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
                    </form>
                </div>
            </div>

        </div>
    @endsection

    @section('javascript')

    @endsection
