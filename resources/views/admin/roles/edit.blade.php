@extends('admin.layouts.admin')

@section('title', 'Edit Role')




@section('admin')
    @include('admin.alert')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Role: {{ $role->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $role->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Permissions</label>
                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->name }}" id="permission_{{ $permission->id }}"
                                            {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Role</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('css')

@endsection

@section('javascript')

@endsection
