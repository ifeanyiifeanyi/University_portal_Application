@extends('admin.layouts.admin')

@section('title', 'Backup Management')

@section('admin')
    @include('admin.alert')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="backupStatus" class="alert" style="display: none;"></div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Backup Management</h3>
                        <div class="card-tools">
                            <!-- Regular Backup Buttons -->
                            <div class="btn-group me-2">
                                <form action="{{ route('admin.backups.create') }}" method="GET" class="d-inline me-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Are you sure you want to create a full backup?')">
                                        <i class="fas fa-archive"></i> Full Backup
                                    </button>
                                </form>

                                <form action="{{ route('admin.backups.files') }}" method="GET" class="d-inline me-1">
                                    @csrf
                                    <button type="submit" class="btn btn-info"
                                        onclick="return confirm('Are you sure you want to backup only files?')">
                                        <i class="fas fa-file"></i> Files Only
                                    </button>
                                </form>

                                <form action="{{ route('admin.backups.database') }}" method="GET" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning"
                                        onclick="return confirm('Are you sure you want to backup only database?')">
                                        <i class="fas fa-database"></i> Database Only
                                    </button>
                                </form>
                            </div>

                            <!-- Urgent Backup Dropdown -->
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-bolt"></i> Urgent Backup
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.backups.urgent', 'full') }}"
                                            onclick="return confirm('Are you sure you want to create an urgent full backup?')">
                                            <i class="fas fa-archive"></i> Full Backup
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.backups.urgent', 'files') }}"
                                            onclick="return confirm('Are you sure you want to create an urgent files backup?')">
                                            <i class="fas fa-file"></i> Files Only
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.backups.urgent', 'db') }}"
                                            onclick="return confirm('Are you sure you want to create an urgent database backup?')">
                                            <i class="fas fa-database"></i> Database Only
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
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
                                        @foreach ($backups as $backup)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($backup['file_name'], 30) }}</td>
                                                <td>
                                                    @switch($backup['backup_type'])
                                                        @case('Database Only')
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-database"></i> Database
                                                            </span>
                                                        @break

                                                        @case('Files Only')
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-file"></i> Files
                                                            </span>
                                                        @break

                                                        @default
                                                            <span class="badge bg-success">
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
                                                    <div class="btn-group">
                                                        <a href="{{ url($backup['download_link']) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                        <a href="{{ route('admin.backups.restore', $backup['file_name']) }}"
                                                            class="btn btn-sm btn-warning"
                                                            onclick="return confirm('Are you sure you want to restore this backup? Current data will be replaced.')">
                                                            <i class="fas fa-undo"></i> Restore
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.backups.delete', $backup['file_name']) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this backup?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
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
        </div>

        

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle urgent backup clicks
                document.querySelectorAll('[data-backup-type]').forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();

                        if (!confirm('Are you sure you want to create an urgent backup?')) {
                            return;
                        }

                        const type = this.dataset.backupType;
                        const statusDiv = document.getElementById('backupStatus');

                        // Show initial status
                        statusDiv.className = 'alert alert-info';
                        statusDiv.style.display = 'block';
                        statusDiv.innerHTML =
                            `<i class="fas fa-spinner fa-spin"></i> Initiating ${type} backup...`;

                        // Disable the button
                        this.disabled = true;

                        // Make the request
                        fetch(`/admin/backups/urgent/${type}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    statusDiv.className = 'alert alert-success';
                                    statusDiv.innerHTML =
                                        `<i class="fas fa-check"></i> ${data.message}`;

                                    // Start polling for status if we have a backup ID
                                    if (data.backup_id) {
                                        pollBackupStatus(data.backup_id);
                                    }
                                } else {
                                    throw new Error(data.message || 'Backup failed');
                                }
                            })
                            .catch(error => {
                                statusDiv.className = 'alert alert-danger';
                                statusDiv.innerHTML =
                                    `<i class="fas fa-exclamation-triangle"></i> ${error.message}`;
                            })
                            .finally(() => {
                                // Re-enable the button
                                this.disabled = false;
                            });
                    });
                });

                function pollBackupStatus(backupId) {
                    const statusDiv = document.getElementById('backupStatus');
                    const interval = setInterval(() => {
                        fetch(`/admin/backups/status/${backupId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'completed') {
                                    statusDiv.className = 'alert alert-success';
                                    statusDiv.innerHTML =
                                        '<i class="fas fa-check"></i> Backup completed successfully!';
                                    clearInterval(interval);
                                    location.reload(); // Refresh to show new backup
                                } else if (data.status === 'failed') {
                                    statusDiv.className = 'alert alert-danger';
                                    statusDiv.innerHTML =
                                        '<i class="fas fa-exclamation-triangle"></i> Backup failed!';
                                    clearInterval(interval);
                                } else {
                                    statusDiv.innerHTML =
                                        `<i class="fas fa-spinner fa-spin"></i> Backup in progress...`;
                                }
                            });
                    }, 5000); // Poll every 5 seconds
                }
            });
        </script>
    @endsection
