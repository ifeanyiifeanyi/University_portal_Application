@extends('admin.layouts.admin')

@section('title', 'Draft Timetables')
@section('admin')
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">

            <a href="{{ route('admin.timetable.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm"></i> New Draft
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="example">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date & Time</th>
                                <th>Course Details</th>
                                <th>Teacher</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($draftTimetables as $timetable)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ $timetable::getDayName($timetable->day_of_week) }}
                                        </div>
                                        <div>{{ $timetable->class_date }}</div>
                                        <small>{{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }} -
                                            {{ Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $timetable->course->code }}</div>
                                        <small>{{ $timetable->course->title }}</small>
                                        <div class="text-muted">
                                            {{ $timetable->department->name }} (Level
                                            {{ $timetable->department->getDisplayLevel($timetable->level) }} - {{ $timetable->level }})
                                        </div>
                                    </td>
                                    <td>{{ Str::title($timetable->teacher->title_and_full_name) }}</td>
                                    <td>{{ $timetable->room }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $timetable->status === 'draft' ? 'warning' : 'info' }}">
                                            {{ ucfirst($timetable->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.timetable.show', $timetable->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.timetable.edit', $timetable->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if ($timetable->status === 'draft')
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="submitForApproval({{ $timetable->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmDelete({{ $timetable->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

    <!-- Modals -->
    {{-- @include('admin.timeTable.partials.approval-modal')
    @include('admin.timeTable.partials.delete-modal') --}}

    @push('scripts')
        <script>
            function submitForApproval(id) {
                if (confirm('Submit this timetable for approval?')) {
                    fetch(`/admin/timetable/${id}/submit-approval`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) location.reload();
                    });
                }
            }

            function confirmDelete(id) {
                if (confirm('Delete this timetable entry?')) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            }

            $(document).ready(function() {
                $('#draftTable').DataTable({
                    order: [
                        [1, 'asc']
                    ],
                    pageLength: 25
                });
            });
        </script>
    @endpush
@endsection
