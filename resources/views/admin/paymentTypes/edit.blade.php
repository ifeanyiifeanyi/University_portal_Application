@extends('admin.layouts.admin')

@section('title', 'Edit Payment Type')

@section('admin')
<div class="container">
    @include('admin.alert')

    <div class="card p-3">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h4>Edit Payment Type</h4>
                <div class="card-body">
                    <form action="{{ route('admin.payment_type.update', $paymentType) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="name">Payment Option Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $paymentType->name) }}" required>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

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

                        <div class="form-group mb-3">
                            <label for="amount">Amount (₦)</label>
                            <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount', $paymentType->amount) }}" required>
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
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

                        <button type="submit" class="btn btn-primary">Update Payment Type</button>
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
