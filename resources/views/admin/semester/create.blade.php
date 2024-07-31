@extends('admin.layouts.admin')

@section('title', isset($semester) ? 'Edit Semester' : 'Create Semester')

@section('admin')
    <div class="container">
        <h1>{{ isset($semester) ? 'Edit Semester' : 'Create Semester' }}</h1>
        <div class="card">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card body mx-3 my-3 px-4 py-4">
                        <form
                            action="{{ isset($semester) ? route('semester-manager.update', $semester) : route('semester-manager.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($semester))
                                @method('PUT')
                            @endif
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $semester->name ?? old('name') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="season">Season</label>
                                <input type="text" class="form-control" id="season" name="season"
                                    value="{{ $semester->season ?? old('season') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $semester->start_date ?? old('start_date') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $semester->end_date ?? old('end_date') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="academic_session_id">Academic Session</label>
                                <select class="form-control" id="academic_session_id" name="academic_session_id" required>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" 
                                            {{ (isset($semester) && $semester->academic_session_id == $session->id) || ($session->is_current && !isset($semester)) ? 'selected' : '' }}
                                            {{ $session->is_current ? '(Current)' : '' }}>
                                            {{ $session->name }} {{ $session->is_current ? '(Current)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                
                            <button type="submit"
                                class="btn btn-primary mt-3">{{ isset($semester) ? 'Update' : 'Create' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
