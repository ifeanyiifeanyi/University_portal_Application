@extends('admin.layouts.admin')
@section('title', 'create bulk ')

@section('admin')
    <h1>Bulk Create Timetable Entries</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('timetables.bulk_store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="academic_session_id">Academic Session:</label>
            <select name="academic_session_id" id="academic_session_id" class="form-control" required>
                @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="semester_id">Semester:</label>
            <select name="semester_id" id="semester_id" class="form-control" required>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="department_id">Department:</label>
            <select name="department_id" id="department_id" class="form-control" required>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="level">Level:</label>
            <input type="number" name="level" id="level" class="form-control" min="1" max="6" required>
        </div>

        <div id="entries-container">
            <div class="entry">
                <h3>Entry 1</h3>
                <div class="form-group">
                    <label for="entries[0][day_of_week]">Day of Week:</label>
                    <select name="entries[0][day_of_week]" class="form-control" required>
                        @for($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}">{{ Timetable::getDayName($i) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label for="entries[0][start_time]">Start Time:</label>
                    <input type="time" name="entries[0][start_time]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="entries[0][end_time]">End Time:</label>
                    <input type="time" name="entries[0][end_time]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="entries[0][course_id]">Course:</label>
                    <select name="entries[0][course_id]" class="form-control" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="entries[0][teacher_id]">Teacher:</label>
                    <select name="entries[0][teacher_id]" class="form-control" required>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="entries[0][room]">Room:</label>
                    <input type="text" name="entries[0][room]" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="button" id="add-entry" class="btn btn-secondary">Add Another Entry</button>
        <button type="submit" class="btn btn-primary">Create Bulk Timetable Entries</button>
    </form>

    <script>
        let entryCount = 1;
        const addEntryButton = document.getElementById('add-entry');
        const entriesContainer = document.getElementById('entries-container');

        addEntryButton.addEventListener('click', function() {
            entryCount++;
            const newEntry = document.createElement('div');
            newEntry.className = 'entry';
            newEntry.innerHTML = `
                <h3>Entry ${entryCount}</h3>
                <div class="form-group">
                    <label for="entries[${entryCount-1}][day_of_week]">Day of Week:</label>
                    <select name="entries[${entryCount-1}][day_of_week]" class="form-control" required>
                        @for($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}">{{ Timetable::getDayName($i) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label for="entries[${entryCount-1}][start_time]">Start Time:</label>
                    <input type="time" name="entries[${entryCount-1}][start_time]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="entries[${entryCount-1}][end_time]">End Time:</label>
                    <input type="time" name="entries[${entryCount-1}][end_time]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="entries[${entryCount-1}][course_id]">Course:</label>
                    <select name="entries[${entryCount-1}][course_id]" class="form-control" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="entries[${entryCount-1}][teacher_id]">Teacher:</label>
                    <select name="entries[${entryCount-1}][teacher_id]" class="form-control" required>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="entries[${entryCount-1}][room]">Room:</label>
                    <input type="text" name="entries[${entryCount-1}][room]" class="form-control" required>
                </div>
            `;
            entriesContainer.appendChild(newEntry);
        });
    </script>
@endsection
