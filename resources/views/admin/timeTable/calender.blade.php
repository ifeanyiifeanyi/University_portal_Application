@extends('admin.layouts.admin')

@section('title', 'Time Table Calendar')

@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
@endsection

@section('admin')
    <div class="container">
        <div class="card p-3">
            <h1>Time Table Calendar</h1>
            <div id='calendar'></div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/admin/timetable/calendar-data',
                eventClick: function(info) {
                    alert('Event: ' + info.event.title + '\nDepartment: ' + info.event.extendedProps.department + '\nRoom: ' + info.event.extendedProps.room);
                }
            });
            calendar.render();
        });
    </script>
@endsection
