@extends('admin.layouts.admin')

@section('title', 'Edit Permission')




@section('admin')
    @include('admin.alert')
    <div class="page-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Manage User Roles</h4>
                <div class="alert alert-info mb-0 py-1">
                    <small><i class="bx bx-info-circle"></i> These are roles managed by the Spatie Permission system</small>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th width="25%">Current Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $index => $admin)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>
                                    {{ $admin->user->full_name }}
                                </td>
                                <td>{{ $admin->user->email }}</td>
                                <td>
                                    @if ($admin->user->roles->count() > 0)
                                        @foreach ($admin->user->roles as $role)
                                            <div class="badge bg-primary me-1 mb-1 d-inline-flex align-items-center">
                                                {{ $role->name }}
                                                <form action="{{ route('admin.admin-users.revoke-role') }}" method="POST"
                                                    class="d-inline ms-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                                                    <input type="hidden" name="role" value="{{ $role->name }}">
                                                    <button type="submit" class="btn btn-link text-white p-0 ms-1"
                                                        style="font-size: 12px;"
                                                        onclick="return confirm('Are you sure you want to revoke this role?')">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No roles assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#assignRolesModal{{ $admin->id }}">
                                        <i class="bx bx-edit"></i> Manage Roles
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal for Managing Roles -->
                            <div class="modal fade" id="assignRolesModal{{ $admin->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Manage Roles: {{ $admin->user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.admin-users.assign-roles') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="admin_id" value="{{ $admin->id }}">

                                                <div class="alert alert-info">
                                                    <small>
                                                        <i class="bx bx-info-circle"></i>
                                                        Select the roles you want to assign to this user.
                                                        Unselecting a role will remove it from the user.
                                                    </small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Available Roles</label>
                                                    <div class="row g-3">
                                                        @foreach ($roles as $role)
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="roles[]" value="{{ $role->name }}"
                                                                        id="role_{{ $admin->id }}_{{ $role->id }}"
                                                                        {{ $admin->user->hasRole($role->name) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="role_{{ $admin->id }}_{{ $role->id }}">
                                                                        {{ $role->name }}
                                                                        @if ($role->permissions->count() > 0)
                                                                            <small class="d-block text-muted">
                                                                                {{ $role->permissions->count() }}
                                                                                permissions
                                                                                <a href="#" data-bs-toggle="tooltip"
                                                                                    data-bs-html="true"
                                                                                    title="@foreach ($role->permissions as $permission){{ $permission->name }}<br> @endforeach">
                                                                                    <i class="bx bx-info-circle"></i>
                                                                                </a>
                                                                            </small>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


@section('css')

@endsection

@section('javascript')

@endsection
