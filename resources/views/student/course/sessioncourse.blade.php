@extends('student.layouts.student')

@section('title', 'Student session selection')
@section('student')
<div class="container-xxl mt-3">
<div class="row">
    <div class="col-xl-12">
        
  
        <div class="card">
          <div class="card-header">
            <h2>Select Session</h2>
          </div>
          <div class="card-body pt-3">
            @include('messages')
            <form action="{{route('student.proceed.session')}}" method="POST">
              @csrf
              <input type="hidden" name="department_id" id="department_id" value="{{$student->department_id}}">
              <input type="hidden" name="student_id" value="{{$student->id}}">
              <input type="hidden" name="user_id" value="{{$student->user_id}}">
            <div class="row mb-3">
                <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Academic session</label>
                <div class="col-md-8 col-lg-9">
                    <select name="session" id="" class="form-control">
                        <option value="" disabled selected>Select session</option>
                        @foreach ($academicsessions as $academicsession)
                        <option value="{{ $academicsession->id }}" {{ $academicsession->is_current ? 'selected' : '' }}>
                            {{ $academicsession->name }} {{ $academicsession->is_current ? '(Current Session)' : '' }}
                        </option>
                    @endforeach
                       
                    </select>
                    @if ($errors->has('session'))
<span class="text-danger">{{$errors->first('session')}}</span>
@endif
                </div>
              </div>
              <div class="row mb-3">
                <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Select level</label>
                <div class="col-md-8 col-lg-9">
                    <select id="level" name="level" class="form-control">
                        <option value="" disabled selected>Select level</option>
                       
                       
                    </select>
                    @if ($errors->has('level'))
<span class="text-danger">{{$errors->first('level')}}</span>
@endif
                </div>
              </div>
              <div class="row mb-3">
                <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Select semester</label>
                <div class="col-md-8 col-lg-9">
                    <select name="semester" id="" class="form-control">
                        <option value="" disabled selected>Select semester</option>
                        @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>
                                        {{ $semester->name }} {{ $semester->is_current ? '(Current Semester)' : '' }}
                                    </option>
                                @endforeach
                       
                    </select>
                    @if ($errors->has('semester'))
<span class="text-danger">{{$errors->first('semester')}}</span>
@endif
                </div>
              </div>

              <div>
                <button class="btn w-50 text-white btn-success" style="">Submit</button>
              </div>
            </form>
          </div>
        </div>

      </div>
</div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const departmentSelect = document.getElementById('department_id');
      const levelSelect = document.getElementById('level');

      function updateLevels() {
          const departmentId = departmentSelect.value;
          fetch(`/student/course_registration/departments/${departmentId}/levels`)
              .then(response => response.json())
              .then(levels => {
                console.log(levels);
                  levelSelect.innerHTML = '';
                  levels.forEach(level => {
                      const option = document.createElement('option');
                      option.value = level;
                      option.textContent = level;
                      levelSelect.appendChild(option);
                  });
              });
      }

      departmentSelect.addEventListener('change', updateLevels);
      updateLevels(); // Initial population
  });
</script>
@endsection