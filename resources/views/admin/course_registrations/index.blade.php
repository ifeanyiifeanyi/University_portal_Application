@extends('admin.layouts.admin')

@section('title', 'Register Courses')

@section('admin')
    <div class="container">
        <h4>Register Courses for <u>{{ $student->user->fullName() }}</u></h4>
        <p>Department: {{ $student->department->name }}</p>
        <p>Current Level: {{ $student->current_level }}</p>
        <p>Maximum Credit Hours: {{ $maxCreditHours }}</p>

        <form action="{{ route('admin.students.register-courses.store', $student->id) }}" method="POST">
            @csrf
            <div class="table-responsive mb-5">
                <h3>Regular Courses</h3>
                <table class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit Hours</th>
                            <th>Enroll</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->course->code }}</td>
                                <td>{{ $assignment->course->title }}</td>
                                <td>{{ $assignment->course->credit_hours }}</td>
                                <td>
                                    <input type="checkbox" name="courses[]" value="{{ $assignment->course_id }}"
                                        {{ in_array($assignment->course_id, $enrolledCourses) ? 'checked disabled' : '' }}
                                        class="course-checkbox" data-credit-hours="{{ $assignment->course->credit_hours }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-5">
                <h3>Carryover Courses</h3>
                <table class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credit Hours</th>
                            <th>Enroll</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carryoverAssignments as $course)
                            <tr>
                                <td>{{ $course->code }}</td>
                                <td>{{ $course->title }}</td>
                                <td>{{ $course->credit_hours }}</td>
                                <td>
                                    <input type="checkbox" name="carryover_courses[]" value="{{ $course->id }}"
                                        {{ in_array($course->id, $enrolledCourses) ? 'checked disabled' : '' }}
                                        class="course-checkbox" data-credit-hours="{{ $course->credit_hours }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <p>Total Credit Hours: <span id="totalCreditHours">0</span></p>
            </div>

            <button type="submit" class="btn btn-primary" id="registerButton">Register Courses</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            const totalCreditHoursElement = document.getElementById('totalCreditHours');
            const registerButton = document.getElementById('registerButton');
            const maxCreditHours = {{ $maxCreditHours }};

            function updateTotalCreditHours() {
                let total = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.getAttribute('data-credit-hours'));
                    }
                });
                totalCreditHoursElement.textContent = total.toFixed(1);

                if (total > maxCreditHours) {
                    registerButton.disabled = true;
                    totalCreditHoursElement.style.color = 'red';
                } else {
                    registerButton.disabled = false;
                    totalCreditHoursElement.style.color = 'inherit';
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalCreditHours);
            });

            updateTotalCreditHours(); // Initial update
        });
    </script>
@endsection
