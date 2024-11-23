@extends('admin.layouts.admin')

@section('title', 'Manage Departments')
@section('css')

@endsection



@section('admin')
    <div class="container">
        {{-- <div class="row">
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

        </div> --}}
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow">
                    <div class="card-header border-0 bg-light">
                        <h5 class="card-title fw-bold">EDIT Department</h5>
                    </div>
                    <form id="departmentForm" method="POST" action="{{ route('admin.department.update', $departmentSingle->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                            value="{{ old('name', $departmentSingle->name ?? '') }}">
                                        <label>Department Name</label>
                                    </div>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                          value="{{ old('phone', $departmentSingle->phone ?? '') }}">
                                        <label>Phone Number</label>
                                    </div>
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                          value="{{ old('email', $departmentSingle->email ?? '') }}">
                                        <label>Email</label>
                                    </div>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select id="faculty_id" name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" oninput="this.setCustomValidity('')" required>
                                            <option value="">Select Faculty</option>
                                            @foreach ($faculties as $faculty)
                                                <option {{ old('faculty_id', $departmentSingle->faculty_id ?? '') == $faculty->id ? 'selected' : '' }} value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                            @endforeach
                                        </select>
                                        <label>Faculty</label>
                                    </div>
                                    @error('faculty_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration"
                                            min="1" max="8"  value="{{ old('duration', $departmentSingle->duration ?? '') }}">
                                        <label>Program Duration (Years)</label>
                                    </div>
                                    @error('duration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select id="program_id" name="program_id" class="form-select">
                                            <option value="">Select Program (Optional)</option>
                                            @foreach ($programs as $program)
                                                <option {{ old('program_id', $departmentSingle->program_id ?? '') == $program->id ? 'selected' : '' }} value="{{ $program->id }}">{{ $program->name }}</option>
                                            @endforeach
                                        </select>
                                        <label>Program</label>
                                    </div>
                                    @error('program_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <select id="department_head_id" name="department_head_id" class="form-select">
                                            <option value="">Select Department Head (Optional)</option>
                                            @foreach ($users as $user)
                                                <option {{ old('department_head_id', $departmentSingle->department_head_id ?? '') == $user->id ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->teacher->title_and_full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label>Department Head</label>
                                    </div>
                                    @error('department_head_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="description" name="description" rows="3" style="height: 100px"
                                            placeholder="Description">{{ old('description', $departmentSingle->description ?? '') }}</textarea>
                                        <label>Description</label>
                                    </div>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-light">
                            <a href="{{ route('admin.department.view') }}" class="btn btn-secondary px-4"><i class="fas fa-times"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-upload"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection

    @section('javascript')

    @endsection
