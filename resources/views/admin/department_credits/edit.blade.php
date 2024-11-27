@extends('admin.layouts.admin')

@section('title', 'Edit Credit Load')
@section('css')

@endsection



@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card py-3 px-3">
                    <form action="{{ route('admin.department.credit.update', $creditAssignment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="department_id">Department</label>
                            <input type="text" class="form-control"
                                value="{{ $departments->find($creditAssignment->department_id)->name }}" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="academic_session_id">Academic Session</label>
                            <input type="text" class="form-control"
                                value="{{ $academicSessions->find($creditAssignment->academic_session_id)->name }}" readonly>
                        </div>


                        <div class="form-group mb-3">
                            <label for="semester_id">Semester</label>
                            <input type="text" class="form-control"
                                value="{{ $semesters->find($creditAssignment->semester_id)->name }}" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label for="level">Level</label>
                            <input type="text" class="form-control" value="{{ $creditAssignment->level }}" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_credit_hours">Max Credit Hours</label>
                            <input type="number" name="max_credit_hours" id="max_credit_hours" class="form-control"
                                value="{{ old('max_credit_hours', $creditAssignment->max_credit_hours ?? '') }}" required
                                min="1">
                            @error('max_credit_hours')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Credit Load</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

@endsection
