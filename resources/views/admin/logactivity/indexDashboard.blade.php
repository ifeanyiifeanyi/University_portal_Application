@extends('admin.layouts.admin')

@section('title', 'Log Activity Archive')


@section('admin')
    @include('admin.alert')
    <div class="container">
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.view.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Activity Log Archives</li>
            </ol>
        </nav>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Activity Log Archives</h5>
                        <!-- Add a button for truncating the activity log table -->
                        <form action="{{ route('activity-log.truncate') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Are you sure you want to clear all activity logs? This action cannot be undone.')">
                                <i class="fas fa-list"></i> Clear Activity Logs
                            </button>
                        </form>
                    </div>
                    <div class="card-body">

                        @if ($archives->isEmpty())
                            <div class="alert alert-info" role="alert">
                                No archives available.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>sn</th>
                                            <th>Size</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($archives as $archive)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}

                                                </td>
                                                <td>{{ number_format($archive['size'] / 1024, 2) }} KB</td>
                                                <td>{{ date('jS M Y', $archive['created_at']) }}</td>
                                                <td>
                                                    <a href="{{ route('activity-archives.download', basename($archive['name'])) }}"
                                                        class="btn btn-sm" style="background-color: #ff00dd;color: white">
                                                        <i class="fas fa-download me-1"></i> Download
                                                    </a>

                                                    <form action="{{ route('activity-archives.delete', $archive['name']) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this archive?')">
                                                            <i class="fas fa-trash"></i> Delete Archive
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('css')

@endsection
@section('javascript')

@endsection
