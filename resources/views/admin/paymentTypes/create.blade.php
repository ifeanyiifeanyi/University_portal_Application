@extends('admin.layouts.admin')

@section('title', 'Create new payment option')

@section('admin')
    <div class="container">
        @include('admin.alert')

        <div class="card p-3">
            @include('admin.return_btn')
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <h4>Create New Payment Option</h4>
                    <div class="card-body">
                        <form action="{{ route('admin.payment_type.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name">Payment Option Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required placeholder="Enter payment name">
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
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
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
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
                                    <div class="form-group mb-3">
                                        <label for="amount">Amount (₦)</label>
                                        <input type="number" class="form-control" id="amount" name="amount"
                                            value="{{ old('amount') }}" required placeholder="Amount">
                                        @error('amount')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
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



                            <div class="form-group mb-3">
                                <label>Apply to:</label>
                                {{-- <div class="form-check">
                                    <input class="form-check-input" type="radio" name="level_type" id="allLevels"
                                        value="all" checked>
                                    <label class="form-check-label" for="allLevels">
                                        All Levels
                                    </label>
                                </div> --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="level_type" id="specificLevels"
                                        value="specific">
                                    <label class="form-check-label" for="specificLevels">
                                        Specific Levels
                                    </label>
                                </div>
                            </div>

                            <div id="levelSelection" class="form-group mb-3" style="display: none;">
                                <label>Select Levels:</label>
                                <div id="levelCheckboxes">
                                    <!-- Checkboxes will be dynamically added here -->
                                </div>
                            </div>


                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="is_active">Check for active payment option</label>
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1">
                                @error('is_active')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Create Payment Option</button>
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
