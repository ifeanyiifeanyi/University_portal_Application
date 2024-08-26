@extends('admin.layouts.admin')

@section('title', 'Register Courses')

@section('admin')
    <div class="container">
        <div class="card p-3">
            <h4>Register Courses for <u>{{ $student->user->fullName() }}</u></h4>
            <p>Department: {{ $student->department->name }}</p>
            <p>Current Level: {{ $student->current_level }}</p>
            <p>Maximum Credit Hours: {{ $maxCreditHours }}</p>
        </div>
        <div class="card p-3">
            <form action="{{ route('admin.students.register-courses.store', $student->id) }}" method="POST">
                @csrf
                <div class="table-responsive mb-5">
                    <h3>Available Courses</h3>
                    <table class="table table-striped w-100">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit Hours</th>
                                <th>Course Level</th>
                                <th>Enroll</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courseAssignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->course->code }}</td>
                                    <td>{{ $assignment->course->title }}</td>
                                    <td>{{ $assignment->course->credit_hours }}</td>
                                    <td>{{ $assignment->level }}</td>
                                    <td>
                                        <input type="checkbox" name="courses[]" value="{{ $assignment->course_id }}"
                                            {{ in_array($assignment->course_id, $enrolledCourses) ? 'checked disabled' : '' }}
                                            class="course-checkbox"
                                            data-credit-hours="{{ $assignment->course->credit_hours }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <p>Total Credit Hours: <span id="totalCreditHours">{{ $totalCreditHours }}</span></p>
                </div>

                <button type="submit" class="btn btn-primary" id="registerButton">Register Courses</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            const totalCreditHoursElement = document.getElementById('totalCreditHours');
            const registerButton = document.getElementById('registerButton');
            const maxCreditHours = {{ $maxCreditHours }};
            const warningElement = document.createElement('p');
            warningElement.id = 'creditHourWarning';
            warningElement.style.color = 'orange';
            totalCreditHoursElement.parentNode.insertBefore(warningElement, totalCreditHoursElement.nextSibling);

            function updateTotalCreditHours() {
                let total = 0;
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.getAttribute('data-credit-hours'));
                    } else if (!checkbox.disabled) {
                        allChecked = false; //added
                    }
                });
                totalCreditHoursElement.textContent = total.toFixed(1);

                if (total > maxCreditHours) {
                    warningElement.textContent =
                        `Warning: Total credit hours (${total.toFixed(1)}) exceed the maximum allowed (${maxCreditHours}).`;
                    registerButton.disabled = true; //added

                } else {
                    warningElement.textContent = '';
                    registerButton.disabled = allChecked; //added

                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalCreditHours);
            });

            updateTotalCreditHours(); // Initial update

            registerButton.addEventListener('click', function(event) {
                const total = parseFloat(totalCreditHoursElement.textContent);
                if (total > maxCreditHours) {
                    if (!confirm(
                            `The total credit hours (${total.toFixed(1)}) exceed the maximum allowed (${maxCreditHours}). Are you sure you want to proceed with the registration?`
                        )) {
                        event.preventDefault();
                    }
                }
            });
        });
    </script>
@endsection
