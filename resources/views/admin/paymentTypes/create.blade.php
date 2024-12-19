@extends('admin.layouts.admin')

@section('title', 'Create new payment option')

@section('admin')
    @include('admin.alert')

    <div class="row">
        <div class="col-md-8 mx-auto card shadow">
            <div class="card-body">
                <form action="{{ route('admin.payment_type.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="name">Payment Option Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required placeholder="Enter payment name">
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="due_date">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                    value="{{ old('due_date') }}" required placeholder="Enter payment due date">
                                @error('due_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-4">
                                <label for="paystack_subaccount_code">Paystack Subaccount Code</label>
                                <input type="string" class="form-control" id="paystack_subaccount_code" name="paystack_subaccount_code"
                                    value="{{ old('paystack_subaccount_code') }}" required placeholder="Paystack subaccount code">
                                @error('paystack_subaccount_code')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="subaccount_percentage">Subaccount Percentage (%)</label>
                                <input type="number" class="form-control" id="subaccount_percentage" name="subaccount_percentage"
                                    value="{{ old('subaccount_percentage') }}" required placeholder="Subaccount percentage">
                                @error('subaccount_percentage')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>



                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="grace_period_days">Grace period (No. of days)</label>
                                <input type="number" class="form-control" id="grace_period_days" name="grace_period_days"
                                    value="{{ old('grace_period_days') }}" required placeholder="Grace period days">
                                @error('grace_period_days')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="late_fee_amount">Late fee amount (₦)</label>
                                <input type="number" class="form-control" id="late_fee_amount" name="late_fee_amount"
                                    value="{{ old('late_fee_amount') }}" required placeholder="Late penalty fee">
                                @error('late_fee_amount')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="is_recurring">Is Payment Recurring ?</label>
                                <select name="is_recurring" id="is_recurring" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0" selected>No</option>
                                </select>
                                @error('is_recurring')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-4">
                                <label for="payment_period">Payment Period:</label>
                                <select name="payment_period" id="payment_period" class="form-control" required>
                                    <option value="" disabled selected>Select Payment Period</option>
                                    <option value="semester">Semester</option>
                                    <option value="session">Academic Session</option>
                                </select>
                                @error('payment_period')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-4">
                                <label for="academic_session_id">Academic Session:</label>
                                <select id="academic_session_id" name="academic_session_id" class="form-control"
                                    required>
                                    <option value="" disabled selected>Select Academic Session</option>

                                    @foreach ($academic_sessions as $as)
                                        <option {{ $as->is_current ? 'selected' : '' }} value="{{ $as->id }}">
                                            {{ $as->name }}</option>
                                    @endforeach
                                </select>
                                @error('academic_session_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-4">
                                <label for="semester_id">Semester:</label>
                                <select id="semester_id" name="semester_id" class="form-control" required>
                                    <option value="" disabled selected>Select Semester</option>

                                    @foreach ($semesters as $ss)
                                        <option {{ $ss->is_current ? 'selected' : '' }} value="{{ $ss->id }}">
                                            {{ $ss->name }}</option>
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
                            <div class="form-group mb-4">
                                <label for="amount">Amount (₦)</label>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    value="{{ old('amount') }}" required placeholder="Fee Amount">
                                @error('amount')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label for="department">Department:</label>
                                <select id="department" name="department_id" class="form-control" required>
                                    <option value="" disabled selected>Select Department</option>
                                    <option value="all">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option {{ old('department_id') == 'department_id' ? 'selected' : '' }}
                                            value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>



                    <div class="form-group mb-4">
                        <label>Apply to:</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="level_type" id="specificLevels"
                                value="specific">
                            <label class="form-check-label" for="specificLevels">
                                Specific Levels
                            </label>
                        </div>
                    </div>

                    <div id="levelSelection" class="form-group mb-4" style="display: none;">
                        <label>Select Levels:</label>
                        <div id="levelCheckboxes">
                            <!-- Checkboxes will be dynamically added here -->
                        </div>
                    </div>


                    <div class="form-group mb-4">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label for="is_active">Check for active payment option</label>
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1">
                        @error('is_active')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create</button>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            function updateLevels() {
                var departmentId = $('#department').val();
                if (departmentId && departmentId !== 'all') {
                    $.get('/admin/department/' + departmentId + '/levels', function(levels) {
                        $('#levelCheckboxes').empty();
                        $.each(levels, function(index, level) {
                            var checkbox = $('<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" name="levels[]" id="level' +
                                level + '" value="' + level + '">' +
                                '<label class="form-check-label" for="level' + level +
                                '">Level ' + level + '</label>' +
                                '</div>');
                            $('#levelCheckboxes').append(checkbox);
                        });
                    });
                } else {
                    $('#levelCheckboxes').empty();
                }
            }

            $('#department').change(updateLevels);

            $('input[name="level_type"]').change(function() {
                if ($(this).val() === 'specific') {
                    $('#levelSelection').show();
                } else {
                    $('#levelSelection').hide();
                }
            });
        });
    </script>
@endsection
