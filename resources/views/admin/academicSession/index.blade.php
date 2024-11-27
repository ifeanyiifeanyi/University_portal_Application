@extends('admin.layouts.admin')

@section('title', 'Academic Session Manager')

@section('admin')
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0">Academic Sessions</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="example">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($academicSessions as $academicSession)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ Str::title($academicSession->name) }}</td>
                                            <td>{{ date('jS F Y', strtotime($academicSession->start_date)) }}</td>
                                            <td>{{ date('jS F Y', strtotime($academicSession->end_date)) }}</td>
                                            <td>
                                                @if ($academicSession->is_current)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Current
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-clock me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end">
                                                    <a href="{{ route('admin.academic.edit', $academicSession->id) }}"
                                                        class="btn btn-sm me-2"><i class="fas fa-edit text-primary"></i></a>

                                                    <a href="javascript:void(0)"
                                                        onclick="confirmDelete('{{ route('admin.academic.delete', $academicSession->id) }}')"
                                                        class="btn btn-sm"><i class="fas fa-trash text-danger"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No academic sessions found.</td>
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
                        <h4 class="mb-0">{{ empty($academicSessionSingle) ? 'Create Session' : 'Edit Session' }}</h4>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ !isset($academicSessionSingle) ? route('admin.academic.store') : route('admin.academic.update', $academicSessionSingle->id) }}"
                            method="post">
                            @csrf

                            @isset($academicSessionSingle)
                                @method('PUT')
                            @endisset

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name', !empty($academicSessionSingle) ? $academicSessionSingle->name : '') }}"
                                    placeholder="Eg. 2024/2025">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="text" name="start_date" class="form-control" id="start_date"
                                    value="{{ old('start_date', !empty($academicSessionSingle) ? date('jS F Y', strtotime($academicSessionSingle->start_date)) : '') }}">
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="text" name="end_date" class="form-control" id="end_date"
                                    value="{{ old('end_date', !empty($academicSessionSingle) ? date('jS F Y', strtotime($academicSessionSingle->end_date)) : '') }}">
                                @error('end_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-check mb-3">
                                <input
                                    {{ !empty($academicSessionSingle) && $academicSessionSingle->is_current == 1 ? 'checked' : '' }}
                                    class="form-check-input" name="is_current" type="checkbox" id="is_current">
                                <label class="form-check-label" for="is_current">Is Current</label>
                            </div>
                            <button type="submit" class="btn btn-secondary w-100">
                                @if (empty($academicSessionSingle))
                                    Register
                                @else
                                    Update
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        function confirmDelete(deleteUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This academic session will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        }
    </script>
@endsection
