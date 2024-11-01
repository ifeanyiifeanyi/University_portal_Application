@extends('admin.layouts.admin')

@section('title', 'Edit Permission')




@section('admin')
    @include('admin.alert')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Permission: {{ $permission->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $permission->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Permission</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('css')

@endsection

@section('javascript')

@endsection
