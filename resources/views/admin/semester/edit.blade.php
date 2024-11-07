@extends('admin.layouts.admin')

@section('title', isset($semester) ? 'Edit Semester' : 'Create Semester')

@section('admin')
    <div class="container">
        @include('admin.alert')
        <h1 class="text-center">{{ isset($semester_manager) ? 'Edit Semester' : 'Create Semester' }}</h1>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card body mx-3 my-3 px-4 py-4">
                    <form action="{{ route('semester-manager.update', $semester_manager) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mt-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $semester_manager->name ?? old('name') }}" required>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="season">Season</label>
                            <input type="text" class="form-control" id="season" name="season"
                                value="{{ $semester_manager->season ?? old('season') }}" required>
                            @error('season')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $semester_manager->start_date ?? old('start_date') }}" required>
                            @error('start_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $semester_manager->end_date ?? old('end_date') }}" required>
                            @error('end_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mt-3 mb-3">
                            <label for="academic_session_id">Academic Session</label>
                            <select class="form-control" id="academic_session_id" name="academic_session_id" required>
                                @foreach ($academicSessions as $session)
                                    <option value="{{ $session->id }}"
                                        {{ isset($semester_manager) && $semester_manager->academic_session_id == $session->id ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_session_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <div class="card p-3 bg-light">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_current" name="is_current"
                                        value="1" {{ $semester_manager->is_current ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_current">Set as Current Semester</label>
                                </div>
                                @if (isset($currentSemester) && (!isset($semester_manager) || $semester_manager->id !== $currentSemester->id))
                                    <small class="text-muted mt-2">
                                        Note: Setting this as current will automatically unset
                                        "{{ $currentSemester->name }}"
                                        as the current semester.
                                    </small>
                                @endif
                                <small class="text-muted d-block mt-2">
                                    * The semester can only be set as current if its date range includes the current date.
                                </small>
                            </div>
                        </div>
                        <button type="submit"
                            class="btn btn-secondary mt-3">{{ isset($semester_manager) ? 'Update' : 'Create' }}</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const isCurrentCheckbox = document.getElementById('is_current');

            // Function to check if date range includes current date
            function checkDateRange() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                const currentDate = new Date();

                if (isCurrentCheckbox.checked && (currentDate < startDate || currentDate > endDate)) {
                    alert('Warning: To set as current semester, the date range must include the current date.');
                    isCurrentCheckbox.checked = false;
                }
            }

            // Add event listeners
            startDateInput.addEventListener('change', checkDateRange);
            endDateInput.addEventListener('change', checkDateRange);
            isCurrentCheckbox.addEventListener('change', checkDateRange);
        });
    </script>
@endsection
