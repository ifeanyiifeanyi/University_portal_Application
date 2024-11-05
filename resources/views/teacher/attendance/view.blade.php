@extends('teacher.layouts.teacher')

@section('title', 'Course Attendees')
@section('css')

@endsection



@section('teacher')
<section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Course Attendees</h5>
            <!-- Table with stripped rows -->
            @include('messages')
            <form action="{{ route('teacher.attendance.update') }}" method="POST">
              @csrf
            <table class="table datatable">
              <thead>
                <tr>
                  <th>
                    Sudent name
                  </th>
                  <th>Department</th>
                  <th>Course name</th>
                 
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
               @forelse ($attendances as $key => $attendance)
             
               <tr>
                <td>{{$attendance->student->user->first_name}} {{$attendance->student->user->last_name}} {{$attendance->student->user->other_name}}</td>
                <td>{{$attendance->department->name}}</td>
                <td>{{$attendance->course->title}}</td>
                <td>
                  <input type="hidden" name="attendance[{{ $key }}][attendance_id]" value="{{$attendance->attendance_id}}">
                  <input type="hidden" name="attendance[{{ $key }}][student_id]" value="{{$attendance->student_id}}">
                  <input type="hidden" name="attendance[{{ $key }}][course_id]" value="{{$attendance->course_id}}">
                  <input type="hidden" name="attendance[{{ $key }}][department_id]" value="{{$attendance->department_id}}">
                  
                  <!-- Checkbox for attendance status -->
                  <input type="checkbox" name="attendance[{{ $key }}][status]" value="present" 
                         @if ($attendance->status == 'present') checked @endif>
                </td>
              </tr>
               @empty
               <tr>
                <td colspan="4">No attendance records found.</td>
              </tr>
               @endforelse
                
             
             
                
              </tbody>
            </table>
          
            <!-- End Table with stripped rows -->
            <button type="submit" class="btn w-100 btn-success" style="color: #ffffff">Submit</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>
@endsection