@extends('admin.layouts.admin')

@section('title', 'Create Permission')




@section('admin')
    @include('admin.alert')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Permission</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('css')

@endsection

@section('javascript')

@endsection
