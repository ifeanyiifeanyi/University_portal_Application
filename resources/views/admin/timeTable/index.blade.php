@extends('admin.layouts.admin')

@section('title', 'Time Table Management')

@section('css')

    <style>
        .timetable-action-btn {
            padding: 0.25rem 0.5rem;
            margin: 0 0.125rem;
        }

        .table th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
        }

        .status-badge {
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
        }

        .status-draft {
            background-color: #ffeeba;
        }

        .status-pending {
            background-color: #b8daff;
        }

        .status-approved {
            background-color: #c3e6cb;
        }

        .status-archived {
            background-color: #e2e3e5;
        }
    </style>
@endsection

@section('admin')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">

            <div class="btn-group">
                <a href="{{ route('admin.timetable.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> New Entry
                </a>
                <a href="{{ route('admin.timetable.draftIndex') }}" class="btn btn-sm btn-info shadow-sm">
                    <i class="fas fa-clock fa-sm text-white-50"></i> Draft Entries
                </a>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="table-tab" data-toggle="tab" href="#table">Table View</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="calendar-tab" data-toggle="tab" href="#calendar">Calendar View</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Table View -->
                    <div class="tab-pane fade show active" id="table">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="timetableTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 1%">S/N</th>
                                        <th style="width: 10%">Day</th>
                                        <th style="width: 15%">Schedule</th>
                                        <th style="width: 20%">Course</th>
                                        <th style="width: 15%">Lecturer</th>
                                        <th style="width: 15%">Department</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timetables as $index => $timetable)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $timetable::getDayName($timetable->day_of_week) }}</td>
                                            <td>
                                                <div>{{ $timetable->class_date }}</div>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }} -
                                                    {{ \Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div>{{ $timetable->course->code }}</div>
                                                <small class="text-muted">{{ $timetable->course->title }}</small>
                                            </td>
                                            <td>{{ $timetable->teacher->title_and_full_name }}</td>
                                            <td>{{ Str::title($timetable->department->name) }}</td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower($timetable->status) }}">
                                                    {{ Str::title(Str::replace('_', ' ', $timetable->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.timetable.show', $timetable->id) }}"
                                                        class="btn btn-sm btn-info timetable-action-btn" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($timetable->isEditable())
                                                        <a href="{{ route('admin.timetable.edit', $timetable->id) }}"
                                                            class="btn btn-sm btn-primary timetable-action-btn"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger timetable-action-btn"
                                                            onclick="confirmDelete({{ $timetable->id }})" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Calendar View -->
                    <div class="tab-pane fade" id="calendar">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <select id="semester_select" class="form-control">
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this timetable entry?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: function(info, successCallback, failureCallback) {
                    fetch(
                            `/admin/timetable/calendar-data?semester_id=${document.getElementById('semester_select').value}`
                        )
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response failed');
                            }
                            return response.json();
                        })
                        .then(data => successCallback(data))
                        .catch(error => {
                            console.error('Error fetching calendar data:', error);
                            failureCallback(error);
                        });
                },
                height: 'auto',
                slotMinTime: '07:00:00',
                slotMaxTime: '21:00:00'
            });
            calendar.render();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#table-tab, #calendar-tab').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        function confirmDelete(id) {
            $('#deleteForm').attr('action', `/admin/timetable/${id}`);
            $('#deleteModal').modal('show');
        }
    </script>
@endsection
