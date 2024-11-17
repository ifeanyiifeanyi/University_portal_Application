@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('admin')
    @include('admin.alert')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Backup Management</h3>
                        <div class="card-tools">
                            <form action="{{ route('admin.backups.create') }}" method="GET" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to create a full backup?')">
                                    <i class="fas fa-archive"></i> Full Backup
                                </button>
                            </form>

                            <form action="{{ route('admin.backups.files') }}" method="GET" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to backup only files?')">
                                    <i class="fas fa-file"></i> Files Only
                                </button>
                            </form>

                            <form action="{{ route('admin.backups.database') }}" method="GET" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to backup only database?')">
                                    <i class="fas fa-database"></i> Database Only
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                    .table-responsive
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>sn</th>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($backups)    --}}
                                @foreach ($backups as $backup)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::limit($backup['file_name'], 30) }}</td>
                                        <td>
                                            {{ $backup['backup_type'] }}
                                            @switch($backup['backup_type'])
                                                @case('Database Only')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-database"></i> Database
                                                    </span>
                                                    @break
                                                @case('Files Only')
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-file"></i> Files
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-archive"></i> Full
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td>{{ $backup['file_size'] }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::createFromTimestamp($backup['created_at'])->diffForHumans() }}
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::createFromTimestamp($backup['created_at'])->format('M d, Y, h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ url($backup['download_link']) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <a href="{{ route('admin.backups.restore', $backup['file_name']) }}"
                                               class="btn btn-sm btn-warning"
                                               onclick="return confirm('Are you sure you want to restore this backup? Current data will be replaced.')">
                                                <i class="fas fa-undo"></i> Restore
                                            </a>
                                            <form action="{{ route('admin.backups.delete', $backup['file_name']) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this backup?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
