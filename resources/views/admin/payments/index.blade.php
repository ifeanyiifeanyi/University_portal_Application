@extends('admin.layouts.admin')

@section('title', 'Make Payments')
@section('css')
    <!-- Add any additional CSS here -->
    <style>
        .card-step {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f1f1;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 18px;
            font-weight: bold;
            margin-right: 16px;
        }

        .card-header {
            display: flex;
            align-items: center;
        }

        .card-body {
            padding: 24px;
        }

        @media (max-width: 767px) {
            .card-step {
                width: 30px;
                height: 30px;
                font-size: 14px;
                margin-right: 12px;
            }

            .card-body {
                padding: 16px;
            }
        }
    </style>
@endsection

@section('admin')
    <div class="container">
        @include('admin.alert')
        <div class="row">
            <div class="col-md-11 mx-auto">
                <div class="">
                    <div class="card-body">
                        <form action="{{ route('admin.payments.submit') }}" method="POST">
                            @csrf

                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="card-step">1</div>
                                    <h5 class="card-title mb-0">Academic Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="academic_session_id">Academic Session</label>
                                                <select name="academic_session_id" id="academic_session_id"
                                                    class="form-control" required>
                                                    <option value="">Select Academic Session</option>
                                                    @foreach ($academicSessions as $session)
                                                        <option {{ $session->is_current ? 'selected' : '' }}
                                                            value="{{ $session->id }}">{{ $session->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="semester_id">Semester</label>
                                                <select name="semester_id" id="semester_id" class="form-control" required>
                                                    <option value="">Select Semester</option>
                                                    @foreach ($semesters as $semester)
                                                        <option {{ $semester->is_current ? 'selected' : '' }}
                                                            value="{{ $semester->id }}">{{ $semester->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="card-step">2</div>
                                    <h5 class="card-title mb-0">Payment Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label for="payment_type_id">Payment Type</label>
                                        <select name="payment_type_id" id="payment_type_id" class="form-control" required>
                                            <option value="">Select Payment Type</option>
                                            @foreach ($paymentTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="card-step">3</div>
                                    <h5 class="card-title mb-0">Department and Level</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3 department-level-student">
                                                <label for="department_id">Department</label>
                                                <select name="department_id" id="department_id" class="form-control"
                                                    required disabled>
                                                    <option value="">Select Department</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3 department-level-student">
                                                <label for="level">Level</label>
                                                <select name="level" id="level" class="form-control" required
                                                    disabled>
                                                    <option value="">Select Level</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="card-step">4</div>
                                    <h5 class="card-title mb-0">Student Selection</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label for="student_id"><b>Select Student</b></label>
                                        <hr>
                                        <table id="example" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Name</th>
                                                    <th>Matric Number</th>
                                                </tr>
                                            </thead>
                                            <tbody id="student-table">
                                                <!-- Students will be dynamically added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="card-step">5</div>
                                    <h5 class="card-title mb-0">Payment Method and Amount</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="payment_method_id">Payment Method</label>
                                                <select name="payment_method_id" id="payment_method_id" class="form-control"
                                                    required>
                                                    <option value="">Select Payment Method</option>
                                                    @foreach ($paymentMethods as $method)
                                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="amount">Amount</label>
                                                <input type="number" name="amount" id="amount" class="form-control"
                                                    required readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-credit-card mr-2"></i>
                                Make Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // $('#payment_type_id').change(function() {
            //     var paymentTypeId = $(this).val();
            //     if (paymentTypeId) {
            //         $.ajax({
            //             url: '{{ route('payments.getDepartmentsAndLevels') }}',
            //             type: 'GET',
            //             data: {
            //                 payment_type_id: paymentTypeId
            //             },
            //             success: function(data) {
            //                 $('#department_id').empty().append(
            //                     '<option value="">Select Department</option>').prop(
            //                     'disabled', false);
            //                 $.each(data.departments, function(key, value) {
            //                     $('#department_id').append('<option value="' + value
            //                         .id + '" data-levels=\'' + JSON.stringify(value
            //                             .levels) + '\'>' + value.name + '</option>');
            //                 });
            //                 $('#amount').val(data.amount);
            //             }
            //         });
            //     } else {
            //         $('#department_id').empty().append('<option value="">Select Department</option>').prop(
            //             'disabled', true);
            //         $('#level').empty().append('<option value="">Select Level</option>').prop('disabled',
            //             true);
            //         $('#student-table').empty();
            //         $('#amount').val('');
            //     }
            // });
            $('#payment_type_id').change(function() {
                var paymentTypeId = $(this).val();
                if (paymentTypeId) {
                    $.ajax({
                        url: '{{ route('payments.getDepartmentsAndLevels') }}',
                        type: 'GET',
                        data: {
                            payment_type_id: paymentTypeId
                        },
                        success: function(data) {
                            // Clear and disable department dropdown initially
                            $('#department_id').empty()
                                .append('<option value="">Select Department</option>')
                                .prop('disabled', false);

                            // Create a map to store unique departments
                            let uniqueDepartments = new Map();

                            // Process departments to remove duplicates
                            data.departments.forEach(function(dept) {
                                // Use department name as key to check for duplicates
                                if (!uniqueDepartments.has(dept.name)) {
                                    uniqueDepartments.set(dept.name, {
                                        id: dept.id,
                                        name: dept.name,
                                        levels: dept.levels
                                    });
                                }
                            });

                            // Add unique departments to dropdown
                            uniqueDepartments.forEach(function(dept) {
                                $('#department_id').append(
                                    '<option value="' + dept.id +
                                    '" data-levels=\'' +
                                    JSON.stringify(dept.levels) + '\'>' + dept
                                    .name + '</option>'
                                );
                            });

                            // Set the amount
                            $('#amount').val(data.amount);
                        }
                    });
                } else {
                    // Reset dropdowns when no payment type is selected
                    $('#department_id')
                        .empty()
                        .append('<option value="">Select Department</option>')
                        .prop('disabled', true);
                    $('#level')
                        .empty()
                        .append('<option value="">Select Level</option>')
                        .prop('disabled', true);
                    $('#student-table').empty();
                    $('#amount').val('');
                }
            });
            $('#department_id').change(function() {
                var levelsData = $(this).find(':selected').data('levels');
                var levels = [];

                if (typeof levelsData === 'string') {
                    try {
                        levels = JSON.parse(levelsData);
                    } catch (e) {
                        console.error("Error parsing levels JSON:", e);
                    }
                } else if (Array.isArray(levelsData)) {
                    levels = levelsData;
                }

                $('#level').empty().append('<option value="">Select Level</option>').prop('disabled',
                    false);
                $.each(levels, function(key, value) {
                    $('#level').append('<option value="' + value + '">' + value + '</option>');
                });
                $('#student-table').empty();
            });

            $('#level').change(function() {
                var departmentId = $('#department_id').val();
                var level = $(this).val();
                var paymentTypeId = $('#payment_type_id').val();
                var academicSessionId = $('#academic_session_id').val();
                var semesterId = $('#semester_id').val();

                if (departmentId && level && paymentTypeId && academicSessionId && semesterId) {
                    $.ajax({
                        url: '{{ route('payments.getStudents') }}',
                        type: 'GET',
                        data: {
                            department_id: departmentId,
                            level: level,
                            payment_type_id: paymentTypeId,
                            academic_session_id: academicSessionId,
                            semester_id: semesterId
                        },
                        success: function(data) {
                            $('#student-table').empty();
                            $.each(data, function(key, value) {
                                $('#student-table').append(
                                    '<tr>' +
                                    '<td><input type="radio" name="student_id" value="' +
                                    value.id + '"></td>' +
                                    '<td>' + value.full_name + '</td>' +
                                    '<td>' + value.matric_number + '</td>' +
                                    '</tr>'
                                );
                            });
                            if (data.length === 0) {
                                $('#student-table').append(
                                    '<tr><td colspan="3" class="text-center">No eligible students found</td></tr>'
                                );
                            }
                        }
                    });
                } else {
                    $('#student-table').empty();
                }
            });

            // Add change event listeners to ensure student list is updated when these fields change
            $('#academic_session_id, #semester_id').change(function() {
                $('#level').trigger('change');
            });
        });
    </script>
@endsection
