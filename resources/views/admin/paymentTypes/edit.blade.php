@extends('admin.layouts.admin')

@section('title', 'Edit Payment Type')

@section('admin')
    @include('admin.alert')

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow py-3 px-3">
                    <form action="{{ route('admin.payment_type.update', $paymentType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Payment Option Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $paymentType->name) }}" required>
                                    @error('name')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="department">Department:</label>
                                    <select id="department" name="department_id" class="form-control" required>
                                        <option value="" disabled>Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ $paymentType->departments->contains($department->id) ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="payment_period">Payment Period:</label>
                                    <select id="payment_period" name="payment_period" class="form-control" required>
                                        <option value="" disabled>Select Payment Period</option>
                                        <option {{ $paymentType->payment_period == 'semester' ? 'selected' : '' }} value="semester">Semester</option>

                                        <option {{ $paymentType->payment_period == 'session' ? 'selected' : '' }} value="session">Academic Session</option>
                                    </select>
                                    @error('semester_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="academic_session">Academic Session:</label>
                                    <select id="academic_session" name="academic_session_id" class="form-control" required>
                                        <option value="" disabled>Select Academic Session</option>
                                        @foreach ($academicSessions as $session)
                                            <option value="{{ $session->id }}" {{ $paymentType->academic_session_id == $session->id ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_session_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-3">
                                    <label for="semester">Semester:</label>
                                    <select id="semester" name="semester_id" class="form-control" required>
                                        <option value="" disabled>Select Semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ $paymentType->semester_id == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
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
                                    <label for="amount">Amount (₦)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount', $paymentType->amount) }}" required>
                                    @error('amount')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="late_fee_amount">Late Fee Amount (₦)</label>
                                    <input type="number" class="form-control" id="late_fee_amount" name="late_fee_amount" value="{{ old('late_fee_amount', $paymentType->late_fee_amount) }}" required>
                                    @error('late_fee_amount')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="grace_period_days">Grace period (No. of Days)</label>
                                    <input type="number" class="form-control" id="grace_period_days" name="grace_period_days"
                                        value="{{ old('grace_period_days', $paymentType->grace_period_days) }}" required placeholder="Grace period days">
                                    @error('grace_period_days')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="due_date">Due Date</label>
                                    <input type="date"
                                           class="form-control"
                                           id="due_date"
                                           name="due_date"
                                           value="{{ old('due_date', $paymentType->due_date ? date('Y-m-d', strtotime($paymentType->due_date)) : '') }}"
                                           required
                                           placeholder="Late penalty fee">
                                    @error('due_date')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="is_recurring">Is Payment Recurring ?</label>
                                    <select name="is_recurring" id="is_recurring" class="form-control" required>
                                        <option {{ $paymentType->is_recurring ? 'selected' : '' }} value="1">Yes</option>
                                        <option {{ $paymentType->is_recurring ? '' : 'selected' }} value="0">No</option>
                                    </select>
                                    @error('is_recurring')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>






                        <div class="form-group mb-3">
                            <label>Levels:</label>
                            <div id="levelCheckboxes">
                                @foreach(range(100, 600, 100) as $level)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="levels[]" id="level{{ $level }}" value="{{ $level }}"
                                            {{ $paymentType->departments->pluck('pivot.level')->contains($level) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="level{{ $level }}">Level {{ $level }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>








                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $paymentType->description) }}</textarea>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $paymentType->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            @error('is_active')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Update</button>
                    </form>
                </div>
            </div>
        </div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#department').change(function() {
        var departmentId = $(this).val();
        if (departmentId) {
            $.get('/admin/department/' + departmentId + '/levels', function(levels) {
                $('#levelCheckboxes').empty();
                $.each(levels, function(index, level) {
                    var checkbox = $('<div class="form-check">' +
                        '<input class="form-check-input" type="checkbox" name="levels[]" id="level' + level + '" value="' + level + '">' +
                        '<label class="form-check-label" for="level' + level + '">Level ' + level + '</label>' +
                        '</div>');
                    $('#levelCheckboxes').append(checkbox);
                });
            });
        }
    });
});
</script>
@endsection
