@extends('admin.layouts.admin')

@section('title', 'Record Attendance')

@section('admin')
    <div class="container">
        <h1>Record New Attendance</h1>
        <form action="{{ route('admin.attendance.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="course_id">Course</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="teacher_id">Teacher</label>
                <select name="teacher_id" id="teacher_id" class="form-control" required>
                    <option value="">Select Teacher</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="academic_session_id">Academic Session</label>
                <select name="academic_session_id" class="form-control" required>
                    @foreach ($academicSessions as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="semester_id">Semester</label>
                <select name="semester_id" class="form-control" required>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="department_id">Department</label>
                <select name="department_id" class="form-control" required>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div id="students-container">
                <!-- Students will be populated here by JavaScript -->
            </div>
            <button type="submit" class="btn btn-primary">Record Attendance</button>
        </form>

    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('course_id').addEventListener('change', function() {
            fetch(`/admin/courses/${this.value}/students`)
                .then(response => response.json())
                .then(data => {
                    const studentContainer = document.getElementById('students-container');
                    studentContainer.innerHTML = '<h3>Students</h3>';
                    data.students.forEach(student => {
                        studentContainer.innerHTML += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="attendance_data[${student.id}]" id="student-${student.id}">
                            <label class="form-check-label" for="student-${student.id}">
                                ${student.user.name}
                            </label>
                        </div>
                    `;
                    });
                });
        });
    </script>

@endsection
