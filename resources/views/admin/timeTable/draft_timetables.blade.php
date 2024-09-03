@extends('admin.layouts.admin')

@section('admin')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Draft Timetables</h1>
        <p class="mb-4">Manage draft timetables, submit for approval, or archive here.</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Draft Timetables</h6>
                <a href="{{ route('admin.timetable.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Draft
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped " id="example" width="100%" cellspacing="0">
                        <thead class="border-dark">
                            <tr  class="border-dark">
                                <th>s/n</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Course</th>
                                <th>Teacher</th>
                                {{-- <th>Room</th> --}}
                                <th>Department</th>
                                {{-- <th>Status</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody  class="border-dark">
                            @foreach ($draftTimetables as $timetable)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $timetable::getDayName($timetable->day_of_week) }}</td>
                                    <td>
                                        {{ $timetable->class_date }}
                                        {{ \Carbon\Carbon::parse($timetable->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($timetable->end_time)->format('h:i A') }}
                                    </td>
                                    <td>{{ $timetable->course->code }} - {{ $timetable->course->name }}</td>
                                    <td>{{ $timetable->teacher->title_and_full_name }}</td>
                                    {{-- <td>{{ $timetable->room }}</td> --}}
                                    <td>{{ $timetable->department->name }}</td>
                                    {{-- <td>{{ $timetable->status }}</td> --}}
                                    <td>
                                        <a href="{{ route('admin.timetable.show', $timetable->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.timetable.edit', $timetable->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.timetable.submitForApproval', $timetable->id) }}"
                                            method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Are you sure you want to submit this timetable for approval?')">
                                                <i class="fas fa-check"></i> Submit for Approval
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.timetable.archive', $timetable->id) }}"
                                            method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                onclick="return confirm('Are you sure you want to archive this timetable?')">
                                                <i class="fas fa-archive"></i> Archive
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.timetable.delete', $timetable->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this timetable?')">
                                                <i class="fas fa-trash"></i>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endpush
