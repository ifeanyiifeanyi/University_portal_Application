@extends('admin.layouts.admin')

@section('title', 'Create Time Table')

@section('css')
    <!-- Add any additional CSS if needed -->
@endsection

@section('admin')
    <div class="container">
        @include('admin.alert')
        <div class="row">
            <div class="col-md-8 mx-auto  shadow card py-3 px-3">


                <form id="createTimetableForm" action="{{ route('admin.timetable.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="academic_session_id">Academic Session:</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-control" required>
                                    <option value="" disabled selected>Select Academic Session</option>
                                    @foreach ($academicSessions as $session)
                                        <option {{ $session->is_current ? 'selected' : '' }} value="{{ $session->id }}">
                                            {{ $session->name }}</option>
                                    @endforeach
                                </select>
                                @error('academic_session_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group mb-3">
                                <label for="semester_id">Semester:</label>
                                <select name="semester_id" id="semester_id" class="form-control" required>
                                    <option value="" disabled selected>Select Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option {{ $semester->is_current ? 'selected' : '' }} value="{{ $semester->id }}">
                                            {{ $session->name }}</option>
                                    @endforeach
                                </select>
                                @error('semester_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="department">Department:</label>
                                <select id="department" name="department_id" class="form-control" required>
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="level">Academic Level:</label>
                                <select id="level" name="level" class="form-control" required>
                                    <option value="" disabled selected>Select Level</option>
                                </select>
                                @error('level')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="course">Course:</label>
                                <select id="course" name="course_id" class="form-control" required>
                                    <option value="" disabled selected>Select Course</option>
                                </select>
                                @error('course_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="teacher">Teacher:</label>
                                <input type="text" id="teacher" name="teacher" class="form-control" readonly>
                                <input type="hidden" id="teacher_id" name="teacher_id" class="form-control" readonly>
                                @error('teacher_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="start_time">Start Time:</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" required>
                                @error('start_time')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="end_time">End Time:</label>
                                <input type="time" name="end_time" id="end_time" class="form-control" required>
                                @error('end_time')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="day_of_week">Day of Week:</label>
                                <select name="day_of_week" id="day_of_week" class="form-control" required>
                                    @for ($i = 1; $i <= 7; $i++)
                                        <option value="{{ $i }}">
                                            {{ \App\Models\TimeTable::getDayName($i) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('day_of_week')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="room">Room:</label>
                                <input type="text" name="room" id="room" class="form-control" required>
                                @error('room')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="class_duration">Duration:</label>
                                <input type="number" name="class_duration" id="class_duration" class="form-control"
                                    required min="1">
                                @error('class_duration')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="class_date">Class Date:</label>
                                <input type="date" name="class_date" id="class_date" class="form-control" required>
                                @error('class_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Timetable
                        Entry</button>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            const departmentSelect = document.getElementById('department');
            const levelSelect = document.getElementById('level');

            function getLevelNumber(levelString) {
                // Mapping object for special format conversions
                const levelMappings = {
                    // ND/HND mappings
                    'ND1': 100,
                    'ND2': 200,
                    'HND1': 300,
                    'HND2': 400,
                    // Nursing mappings
                    'RN1': 100,
                    'RN2': 200,
                    'RN3': 300,
                    // Midwifery mappings
                    'RMW1': 100,
                    'RMW2': 200,
                    'RMW3': 300
                };

                // If it's already numeric, return as integer
                if (!isNaN(levelString)) {
                    return parseInt(levelString);
                }

                // Return mapped value or null if not found
                return levelMappings[levelString] || null;
            }

            function updateLevels() {
                var departmentId = $('#department').val();
                if (departmentId) {
                    $.get('/admin/department/' + departmentId + '/levels', function(levels) {
                        $('#level').empty();
                        $.each(levels, function(index, level) {
                            $('#level').append($('<option>', {
                                value: level,
                                text: 'Level ' + level
                            }));
                        });
                        updateCourses();
                    });
                }
            }

            function updateLevels() {
                const departmentId = departmentSelect.value;
                if (!departmentId) {
                    levelSelect.innerHTML = '<option value="">Select Department First</option>';
                    return;
                }

                fetch(`/admin/departments/${departmentId}/levels`)
                    .then(response => response.json())
                    .then(levels => {
                        levelSelect.innerHTML = '<option value="">Select Level</option>';

                        levels.forEach(level => {
                            const option = document.createElement('option');
                            const numericValue = getLevelNumber(level);

                            // Set the display text
                            option.textContent = level;

                            // Set the value to the numeric equivalent
                            option.value = numericValue || level;

                            levelSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading levels:', error);
                        levelSelect.innerHTML = '<option value="">Error loading levels</option>';
                    });
            }

            function updateCourses() {
                var departmentId = $('#department').val();
                var level = $('#level').val();
                if (departmentId && level) {
                    $.get('/admin/courses', {
                        department_id: departmentId,
                        level: level
                    }, function(courses) {
                        $('#course').empty();
                        $.each(courses, function(index, course) {
                            $('#course').append($('<option>', {
                                value: course.id,
                                text: course.code + ' - ' + course.title
                            }));
                        });
                        updateTeacher();
                    });
                }
            }

            function updateTeacher() {
                var courseId = $('#course').val();
                var departmentId = $('#department').val();
                var level = $('#level').val();
                if (courseId && departmentId && level) {
                    $.get('/admin/course-assignment', {
                        course_id: courseId,
                        department_id: departmentId,
                        level: level
                    }, function(data) {
                        console.log(data)
                        $('#teacher').val(data.teacher_name);
                        $('#teacher_id').val(data.teacher_id);
                    });
                }
            }


            $('#department').change(updateLevels);
            $('#level').change(updateCourses);
            $('#course').change(updateTeacher);

            // Trigger updates on page load
            // Add event listeners
            departmentSelect.addEventListener('change', updateLevels);

            // Initialize on page load if department is pre-selected
            if (departmentSelect.value) {
                updateLevels();
            }


        });
    </script>
@endsection
