@extends('admin.layouts.admin')

@section('title', 'Semester Manager')

@section('admin')
    <div class="container card py-3 px-3">
        <h4><i class="fas fa-calendar-alt me-2"></i>Semester Manager</h4>
        <div class="row mb-5">
            <div class="col-md-6">
                <a href="{{ route('semester-manager.create') }}" class="btn btn-secondary">
                    <i class="fas fa-plus-circle me-1"></i> Create New Semester
                </a>
            </div>
            <div class="col-md-6">
                <form action="{{ route('semester.manager.search') }}" method="GET">
                    <div class="input-group">
                        <input type="search" class="form-control" name="search" placeholder="Search semesters...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <form action="{{ route('semester.manager.bulk-action') }}" method="POST" id="bulkActionForm">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <select name="action" class="form-select d-inline-block w-auto mr-2">
                    <option value="delete">Delete Selected</option>
                    <option value="change_session">Change Academic Session</option>
                </select>
                <select name="new_session" class="form-select d-inline-block w-auto mr-2">
                    @foreach ($academicSessions as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="confirmBulkAction()" class="btn btn-secondary">
                    <i class="fas fa-tasks me-1"></i> Apply
                </button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Name</th>
                        <th>Season</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Academic Session</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($semesters as $semester)
                        <tr>
                            <td>
                                @if ($semester->canBeDeleted())
                                    <input type="checkbox" name="semesters[]" value="{{ $semester->id }}">
                                @else
                                    <input type="checkbox" disabled title="This semester cannot be deleted">
                                @endif
                            </td>
                            <td>
                                {{ $semester->name }}
                                @if ($semester->is_current == true)
                                    <p><span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Current Semester
                                    </span></p>
                                @endif
                            </td>
                            <td><i class="fas fa-sun me-1"></i>{{ $semester->season }}</td>
                            <td><i class="fas fa-calendar-day me-1"></i>{{ $semester->start_date }}</td>
                            <td><i class="fas fa-calendar-day me-1"></i>{{ $semester->end_date }}</td>
                            <td>
                                {{ $semester->academicSession->name }}
                                @if ($semester->academicSession->is_current == true)
                                    <p><span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Current Session
                                    </span></p>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('semester-manager.toggle-current', $semester) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $semester->is_current ? 'btn-success' : 'btn-secondary' }}">
                                        <i class="fas {{ $semester->is_current ? 'fa-check-circle' : 'fa-circle' }} me-1"></i>
                                        {{ $semester->is_current ? 'Current' : 'Set Current' }}
                                    </button>
                                </form>
                                <a href="{{ route('semester-manager.edit', $semester) }}"
                                    class="btn btn-sm text-primary"><i class="fas fa-edit"></i></a>
                                @if ($semester->canBeDeleted())
                                    <button type="button" class="btn btn-sm text-danger"
                                        onclick="confirmDelete('{{ route('semester-manager.destroy', $semester) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <x-disable-icon />
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>

    <script>
        function confirmDelete(deleteUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This semester will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmBulkAction() {
            const actionSelect = document.querySelector('select[name="action"]');
            const selectedAction = actionSelect.value;
            let title, text;

            if (selectedAction === 'delete') {
                title = 'Delete Selected Semesters?';
                text = 'This action cannot be undone!';
            } else {
                title = 'Change Academic Session?';
                text = 'This will update the academic session for all selected semesters.';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulkActionForm').submit();
                }
            });
        }
    </script>
@endsection
