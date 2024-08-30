<!DOCTYPE html>
<html>
<head>
    <title>Timetable</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Timetable</h1>
    <table>
        <tr>
            <th>Day</th>
            <th>Time</th>
            <th>Course</th>
            <th>Teacher</th>
            <th>Room</th>
        </tr>
        <tr>
            <td>{{ $timetable->day_of_week }}</td>
            <td>{{ $timetable->start_time }} - {{ $timetable->end_time }}</td>
            <td>{{ $timetable->course->name }}</td>
            <td>{{ $timetable->teacher->name }}</td>
            <td>{{ $timetable->room }}</td>
        </tr>
    </table>
</body>
</html>
