@extends('admin.layouts.admin')

@section('title', 'Academic Session Manager')
@section('css')
    <style>
        .table-responsive {
            overflow: visible;
        }

        .custom-dropdown-menu {
            z-index: 1050;
            /* Adjust this value if necessary */
            position: relative;
        }
    </style>
@endsection


@section('admin')
    <div class="row">
        <div class="col-md-7">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @dd($academicSessions) --}}
                        @forelse ($academicSessions as $academicSession)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::title($academicSession->name) }}</td>
                                <td>{{ $academicSession->start_date }}</td>
                                <td>{{ $academicSession->end_date }}</td>
                                <td>
                                    @if ($academicSession->is_current == 1)
                                        <button style="background: teal;border:none" type="button"
                                            class="btn btn-primary position-relative">Current <span
                                                class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-success p-2"><span
                                                    class="visually-hidden">unread messages</span></span>
                                        </button>
                                    @else
                                        <span> --------------- </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="col">
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                            <ul class="dropdown-menu custom-dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.academic.edit', $academicSession->id) }}">Edit</a>
                                                </li>
                                                <li><a class="dropdown-item" href="#">Associations</a></li>
                                                <li><a class="dropdown-item" href="#"> ----------------- </a></li>
                                                <li><a onclick="return confirm('Are you sure of this action ?')"
                                                        class="dropdown-item text-danger"
                                                        href="{{ route('admin.academic.delete', $academicSession->id) }}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        <div class="col-md-5">
            <form
                action="{{ !isset($academicSessionSingle) ? route('admin.academic.store') : route('admin.academic.update', $academicSessionSingle->id) }}"
                method="post">
                @csrf

                @isset($academicSessionSingle)
                    @method('PUT')
                @endisset

                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            @if (empty($academicSessionSingle))
                                <h5 class="mb-0 text-primary">Create Sessions</h5>
                            @else
                                <h5 class="mb-0 text-primary">Edit Session</h5>
                            @endif
                        </div>
                        <hr>
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ old('name', !empty($academicSessionSingle) ? $academicSessionSingle->name : '') }}"
                                placeholder="Eg. 2024/2025">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" id="start_date"
                                value="{{ old('start_date', !empty($academicSessionSingle) ? $academicSessionSingle->start_date : '') }}">
                            @error('start_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" id="end_date"
                                value="{{ old('end_date', !empty($academicSessionSingle) ? $academicSessionSingle->end_date : '') }}">
                            @error('end_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input
                                    {{ !empty($academicSessionSingle) && $academicSessionSingle->is_current == 1 ? 'checked' : '' }}
                                    class="form-check-input" name="is_current" type="checkbox" id="gridCheck">
                                <label class="form-check-label" for="gridCheck">Is Current</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-5">
                                @if (empty($academicSessionSingle))
                                    Register
                                @else
                                    Update
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('javascript')

@endsection
