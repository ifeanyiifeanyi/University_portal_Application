@extends('admin.layouts.admin')

@section('title', 'Assign Credit Load')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card py-3 px-3">
                    <form action="{{ route('admin.department.credit.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="department_id">Department</label>
                            <select name="department_id" id="department_id" class="form-control " required>
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option {{ old('department_id') == $department->id ? 'selected' : '' }}
                                        value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group mb-3">
                            <label for="academic_session_id">Academic Session</label>
                            <select name="academic_session_id" id="academic_session_id" class="form-control single-select" required>
                                @foreach ($academicSessions as $session)
                                    <option {{ $session->is_current ? 'selected' : '' }} value="{{ $session->id }}">
                                        {{ $session->name }}</option>
                                @endforeach
                            </select>
                            @error('academic_session_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group mb-3">
                            <label for="semester_id">Semester</label>
                            <select name="semester_id" id="semester_id" class="form-control single-select" required>
                                @foreach ($semesters as $semester)
                                    <option {{ $semester->is_current ? 'selected' : '' }} value="{{ $semester->id }}">
                                        {{ $semester->name }}</option>
                                @endforeach
                            </select>
                            @error('semester_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="level">Level</label>
                            <select name="level" id="level" class="form-control" required>
                                <option value="">Select Semester First</option>
                            </select>
                            @error('level')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_credit_hours">Max Credit Hours</label>
                            <input type="number" name="max_credit_hours" id="max_credit_hours" class="form-control"
                                required min="1" value="{{ old('max_credit_hours') }}">
                            @error('max_credit_hours')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-tasks"></i> Assign Credit Load</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            const levelSelect = document.getElementById('level');

            function updateLevels() {
                const departmentId = departmentSelect.value;
                if (departmentId) {
                    fetch(`/admin/departments/${departmentId}/levels`)
                        .then(response => response.json())
                        .then(levels => {
                            levelSelect.innerHTML = '';
                            levels.forEach(level => {
                                const option = document.createElement('option');
                                option.value = level;
                                option.textContent = level;
                                levelSelect.appendChild(option);
                            });
                        });
                } else {
                    levelSelect.innerHTML = '<option value="">Select Department First</option>';
                }
            }

            departmentSelect.addEventListener('change', updateLevels);
            updateLevels(); // Initial population
        });
    </script>

@endsection
