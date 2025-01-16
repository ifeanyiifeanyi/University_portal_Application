@extends('admin.layouts.admin')

@section('title', 'Send Bulk Emails')

@section('admin')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Send Bulk Emails</h5>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <select id="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Level</label>
                        <select id="level" class="form-select">
                            <option value="">All Levels</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-secondary mt-4" id="filterStudents">Filter Students</button>
                    </div>
                </div>
                <!-- Email Form -->
                <form id="bulkEmailForm" action="{{ route('admin.student.email.send-bulk') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Student Selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6>Selected Students</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-secondary" id="selectAll">Select All</button>
                                <button type="button" class="btn btn-sm btn-secondary" id="deselectAll">Deselect
                                    All</button>
                            </div>
                        </div>
                        <div id="studentList" class="border p-3" style="max-height: 300px; overflow-y: auto;">
                            <!-- Students will be loaded here -->
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" class="form-control" multiple>
                    </div>
                    <button type="submit" class="btn btn-primary" id="sendBulkEmail">Send Emails</button>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Update levels when department changes
            $('#department').change(function() {
                const departmentId = $(this).val();
                if (departmentId) {
                    $.get(`/admin/departments/${departmentId}`, function(data) {
                        const levelSelect = $('#level');
                        levelSelect.empty();
                        levelSelect.append('<option value="">All Levels</option>');

                        // Map display values to numeric values
                        const displayToNumeric = {
                            // For ND/HND
                            'ND1': '100',
                            'ND2': '200',
                            'HND1': '300',
                            'HND2': '400',
                            // For RN
                            'RN1': '100',
                            'RN2': '200',
                            'RN3': '300'
                        };

                        data.levels.forEach(level => {
                            const numericValue = displayToNumeric[level] || level;
                            levelSelect.append(
                                `<option value="${numericValue}">${level}</option>`);
                        });
                    });
                } else {
                    $('#level').empty().append('<option value="">All Levels</option>');
                }
            });

            // Filter students
            $('#filterStudents').click(function() {
                $.get('/admin/students/filter', {
                    department_id: $('#department').val(),
                    current_level: $('#level').val()
                }, function(response) {
                    const studentList = $('#studentList');
                    studentList.empty();

                    response.students.forEach(student => {
                        studentList.append(`
                        <div class="form-check">
                            <input class="form-check-input student-checkbox" type="checkbox"
                                name="student_ids[]" value="${student.id}" id="student${student.id}">
                            <label class="form-check-label" for="student${student.id}">
                                ${student.name} - ${student.email} (${student.department}, Level ${student.level})
                            </label>
                        </div>
                    `);
                    });
                });
            });

            // Select/Deselect All
            $('#selectAll').click(() => $('.student-checkbox').prop('checked', true));
            $('#deselectAll').click(() => $('.student-checkbox').prop('checked', false));

            // Form submission
            $('#bulkEmailForm').submit(function(e) {
                const selectedStudents = $('.student-checkbox:checked');
                if (selectedStudents.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one student');
                }
            });
        });
    </script>
@endsection
