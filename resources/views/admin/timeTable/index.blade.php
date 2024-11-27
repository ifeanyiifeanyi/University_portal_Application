@extends('admin.layouts.admin')
@section('title', 'Time Table Manager')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core/main.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid/main.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid/main.css" rel="stylesheet" />
@endsection
@section('admin')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Timetables</h6>
                <div>
                    <a href="{{ route('admin.timetable.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                    <a href="" class="btn btn-info btn-sm">
                        <i class="fas fa-check"></i> Pending Approvals
                    </a>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="table-tab" data-toggle="tab" href="#table" role="tab"
                            aria-controls="table" aria-selected="false">Table View</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab"
                            aria-controls="calendar" aria-selected="true">Calendar View</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="table" role="tabpanel" aria-labelledby="table-tab">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Room</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timetables as $timetable)
                                        <tr>
                                            <td>{{ $timetable->day_of_week }}</td>
                                            <td>{{ $timetable->start_time }} - {{ $timetable->end_time }}</td>
                                            <td>{{ $timetable->course->code }} - {{ $timetable->course->name }}</td>
                                            <td>{{ $timetable->teacher->user->name }}</td>
                                            <td>{{ $timetable->room }}</td>
                                            <td>{{ $timetable->department->name }}</td>
                                            <td>{{ $timetable->status }}</td>
                                            <td>
                                                <a href="{{ route('admin.timetable.show', $timetable->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.timetable.edit', $timetable->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.timetable.delete', $timetable->id) }}"
                                                    method="POST" style="display: inline-block;">
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
                    <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                        <div class="form-group mt-3">
                            <label for="semester_select">Select Semester:</label>
                            <select id="semester_select" class="form-control">
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection




@section('javascript')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script>
        $(document).ready(function() {

            // var calendar = $('#calendar').fullCalendar({
            //     header: {
            //         left: 'prev,next today',
            //         center: 'title',
            //         right: 'month,agendaWeek,agendaDay'
            //     },
            //     eventColor: function(event) {
            //         return event.color;
            //     },



            //     events: function(start, end, timezone, callback) {
            //         var selectedSemester = $('#semester_select').val();
            //         $.ajax({
            //             url: '{{ route('admin.timetable.calendar-data') }}',
            //             data: {
            //                 semester_id: selectedSemester,
            //                 start: start.format(),
            //                 end: end.format()
            //             },
            //             success: function(response) {
            //                 console.log(response)
            //                 var events = response.map(function(event) {
            //                     return {
            //                         id: event.id,
            //                         title: event.title,
            //                         start: event.start,
            //                         end: event.end,
            //                         color: event.color,
            //                         textColor: event.textColor,
            //                         extendedProps: event.extendedProps,
            //                         rrule: event
            //                             .rrule // This will now come from the server
            //                     };
            //                 });
            //                 callback(events);
            //             }
            //         });
            //     },


            //     eventRender: function(event, element) {
            //         element.find('.fc-title').append("<br/>" + event.extendedProps.room);
            //     }
            // });

            var calendar = $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: function(start, end, timezone, callback) {
                    console.log("Fetching events...");
                    var selectedSemester = $('#semester_select').val();
                    $.ajax({
                        url: '{{ route('admin.timetable.calendar-data') }}',
                        data: {
                            semester_id: selectedSemester,
                            start: start.format(),
                            end: end.format()
                        },
                        success: function(response) {
                            console.log("Received response:", response);
                            var events = response.map(function(event) {
                                console.log("Processing event:", event);
                                return {
                                    id: event.id,
                                    title: event.title,
                                    startTime: event.startTime,
                                    endTime: event.endTime,
                                    startRecur: event.startRecur,
                                    endRecur: event.endRecur,
                                    daysOfWeek: event.daysOfWeek,
                                    textColor: event.textColor,
                                    color: "red",
                                    extendedProps: event.extendedProps
                                };
                            });
                            console.log("Processed events:", events);
                            callback(events);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching events:", error);
                        }
                    });
                },
                eventRender: function(event, element) {
                    console.log("Rendering event:", event);
                    element.css('background-color', event.extendedProps.color);
                    element.find('.fc-title').append("<br/>" + event.extendedProps.room);
                },
                eventAfterAllRender: function(view) {
                    console.log("All events rendered");
                }


            });

            $('#semester_select').change(function() {
                calendar.fullCalendar('refetchEvents');
            });
        });
    </script>
@endsection
