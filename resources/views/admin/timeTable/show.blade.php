@extends('admin.layouts.admin')

@section('title', 'Timetable Details')
@section('admin')
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">


        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-sm-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Schedule Information</h6>
                        <div class="btn-group">
                            @if ($timetable->status === 'pending_approval' )
                                <button type="button" class="btn btn-success btn-sm" onclick="approveTimetable({{ $timetable->id }})">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectTimetable({{ $timetable->id }})">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @endif
                            <a href="{{ route('admin.timetable.edit', $timetable->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Day</dt>
                                    <dd class="col-sm-8">{{ $timetable::getDayName($timetable->day_of_week) }}</dd>

                                    <dt class="col-sm-4">Date</dt>
                                    <dd class="col-sm-8">{{ $timetable->class_date }}</dd>

                                    <dt class="col-sm-4">Time</dt>
                                    <dd class="col-sm-8">
                                        {{ Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }} -
                                        {{ Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}
                                    </dd>

                                    <dt class="col-sm-4">Duration</dt>
                                    <dd class="col-sm-8">{{ $timetable->class_duration }} Hrs</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Course</dt>
                                    <dd class="col-sm-8">
                                        {{ $timetable->course->code }} - {{ $timetable->course->title }}
                                    </dd>

                                    <dt class="col-sm-4">Teacher</dt>
                                    <dd class="col-sm-8">{{ $timetable->teacher->title_and_full_name }}</dd>

                                    <dt class="col-sm-4">Room</dt>
                                    <dd class="col-sm-8">{{ $timetable->room }}</dd>

                                    <dt class="col-sm-4">Status</dt>
                                    <dd class="col-sm-8">
                                        <span
                                            class="badge bg-{{ $timetable->status === 'approved' ? 'success' : 'warning' }}">
                                            {{ Str::title( Str::replace('_', ' ', $timetable->status)) }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">Department</dt>
                            <dd class="col-sm-7">{{ Str::title($timetable->department->name) }}</dd>

                            <dt class="col-sm-5">Level</dt>
                            <dd class="col-sm-7">{{ $timetable->department->getDisplayLevel($timetable->level) }}</dd>

                            <dt class="col-sm-5">Session</dt>
                            <dd class="col-sm-7">{{ $timetable->academicSession->name }}</dd>

                            <dt class="col-sm-5">Semester</dt>
                            <dd class="col-sm-7">{{ $timetable->semester->name }}</dd>

                            <dt class="col-sm-5">Created By</dt>
                            <dd class="col-sm-7">{{ $timetable->creator->full_name }}</dd>

                            <dt class="col-sm-5">Created At</dt>
                            <dd class="col-sm-7">{{ $timetable->created_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function approveTimetable(id) {
                if (confirm('Approve this timetable?')) {
                    fetch(`/admin/timetable/${id}/approve`, {
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

            function rejectTimetable(id) {
                const reason = prompt('Please provide a reason for rejection:');
                if (reason) {
                    fetch(`/admin/timetable/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            reason
                        })
                    }).then(response => {
                        if (response.ok) location.reload();
                    });
                }
            }
        </script>
    @endpush
@endsection
