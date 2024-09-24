@extends('admin.layouts.admin')

@section('title', 'Make Payments')
@section('css')

@endsection



@section('admin')

    <div class="container">
        @include('admin.alert')
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.payments.submit') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="academic_session_id">Academic Session</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-control" required>
                                    <option value="">Select Academic Session</option>
                                    @foreach ($academicSessions as $session)
                                        <option {{ $session->is_current ? 'selected' : '' }} value="{{ $session->id }}">
                                            {{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="semester_id">Semester</label>
                                <select name="semester_id" id="semester_id" class="form-control" required>
                                    <option value="">Select Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option {{ $semester->is_current ? 'selected' : '' }} value="{{ $semester->id }}">
                                            {{ $semester->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="payment_type_id">Payment Type</label>
                                <select name="payment_type_id" id="payment_type_id" class="form-control" required>
                                    <option value="">Select Payment Type</option>
                                    @foreach ($paymentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="department_id">Department</label>
                                <select name="department_id" id="department_id" class="form-control" required disabled>
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="level">Level</label>
                                <select name="level" id="level" class="form-control" required disabled>
                                    <option value="">Select Level</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="student_id">Student</label>
                                <select name="student_id" id="student_id" class="form-control" required disabled>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="payment_method_id">Payment Method</label>
                                <select name="payment_method_id" id="payment_method_id" class="form-control" required>
                                    <option value="">Select Payment Method</option>
                                    @foreach ($paymentMethods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="amount">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" required readonly>
                            </div>
                            <button onclick="return confirm('Are you sure of this action ?')" type="submit" class="btn btn-primary">Make Payment</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // $(document).ready(function() {
        //     $('#payment_type_id').change(function() {
        //         var paymentTypeId = $(this).val();
        //         if (paymentTypeId) {
        //             $.ajax({
        //                 url: '{{ route('payments.getDepartmentsAndLevels') }}',
        //                 type: 'GET',
        //                 data: {
        //                     payment_type_id: paymentTypeId
        //                 },
        //                 success: function(data) {
        //                     $('#department_id').empty().append(
        //                         '<option value="">Select Department</option>').prop(
        //                         'disabled', false);
        //                     $.each(data, function(key, value) {
        //                         $('#department_id').append('<option value="' + value
        //                             .id + '" data-levels=\'' + JSON.stringify(value
        //                                 .levels) + '\'>' + value.name + '</option>');
        //                     });
        //                 }
        //             });
        //         } else {
        //             $('#department_id').empty().append('<option value="">Select Department</option>').prop(
        //                 'disabled', true);
        //             $('#level').empty().append('<option value="">Select Level</option>').prop('disabled',
        //                 true);
        //             $('#student_id').empty().append('<option value="">Select Student</option>').prop(
        //                 'disabled', true);
        //         }
        //     });

        //     $('#department_id').change(function() {
        //         var levelsData = $(this).find(':selected').data('levels');
        //         var levels = [];

        //         if (typeof levelsData === 'string') {
        //             try {
        //                 levels = JSON.parse(levelsData);
        //             } catch (e) {
        //                 console.error("Error parsing levels JSON:", e);
        //             }
        //         } else if (Array.isArray(levelsData)) {
        //             levels = levelsData;
        //         }

        //         $('#level').empty().append('<option value="">Select Level</option>').prop('disabled',
        //             false);
        //         $.each(levels, function(key, value) {
        //             $('#level').append('<option value="' + value + '">' + value + '</option>');
        //         });
        //         $('#student_id').empty().append('<option value="">Select Student</option>').prop('disabled',
        //             true);
        //     });

        //     $('#level').change(function() {
        //         var departmentId = $('#department_id').val();
        //         var level = $(this).val();
        //         if (departmentId && level) {
        //             $.ajax({
        //                 url: '{{ route('payments.getStudents') }}',
        //                 type: 'GET',
        //                 data: {
        //                     department_id: departmentId,
        //                     level: level
        //                 },
        //                 success: function(data) {
        //                     console.log('data: ', data);

        //                     $('#student_id').empty().append(
        //                         '<option value="">Select Student</option>').prop('disabled',
        //                         false);
        //                     $.each(data, function(key, value) {
        //                         $('#student_id').append('<option value="' + value.id +
        //                             '">' + value.first_name + ' (' + value
        //                             .matric_number + ')</option>');
        //                     });
        //                 }
        //             });
        //         } else {
        //             $('#student_id').empty().append('<option value="">Select Student</option>').prop(
        //                 'disabled', true);
        //         }
        //     });

        //     $('#payment_type_id, #department_id, #level, #student_id').change(function() {
        //         var paymentTypeId = $('#payment_type_id').val();
        //         var departmentId = $('#department_id').val();
        //         var level = $('#level').val();
        //         var studentId = $('#student_id').val();
        //         if (paymentTypeId && departmentId && level && studentId) {
        //             $.ajax({
        //                 url: '{{ route('payment-types.getAmount') }}',
        //                 type: 'GET',
        //                 data: {
        //                     payment_type_id: paymentTypeId,
        //                     department_id: departmentId,
        //                     level: level,
        //                     student_id: studentId
        //                 },
        //                 success: function(data) {
        //                     $('#amount').val(data.amount);
        //                 }
        //             });
        //         } else {
        //             $('#amount').val('');
        //         }
        //     });
        // });

        $(document).ready(function() {
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
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>').prop(
                                'disabled', false);
                            $.each(data.departments, function(key, value) {
                                $('#department_id').append('<option value="' + value
                                    .id + '" data-levels=\'' + JSON.stringify(value
                                        .levels) + '\'>' + value.name + '</option>');
                            });
                            $('#amount').val(data.amount);
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>').prop(
                        'disabled', true);
                    $('#level').empty().append('<option value="">Select Level</option>').prop('disabled',
                        true);
                    $('#student_id').empty().append('<option value="">Select Student</option>').prop(
                        'disabled', true);
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
                $('#student_id').empty().append('<option value="">Select Student</option>').prop('disabled',
                    true);
            });

            $('#level').change(function() {
                var departmentId = $('#department_id').val();
                var level = $(this).val();
                if (departmentId && level) {
                    $.ajax({
                        url: '{{ route('payments.getStudents') }}',
                        type: 'GET',
                        data: {
                            department_id: departmentId,
                            level: level
                        },
                        success: function(data) {
                            $('#student_id').empty().append(
                                '<option value="">Select Student</option>').prop('disabled',
                                false);
                            $.each(data, function(key, value) {
                                $('#student_id').append('<option value="' + value.id +
                                    '">' + value.full_name + ' (' + value
                                    .matric_number + ')</option>');
                            });
                        }
                    });
                } else {
                    $('#student_id').empty().append('<option value="">Select Student</option>').prop(
                        'disabled', true);
                }
            });
        });
    </script>
@endsection
