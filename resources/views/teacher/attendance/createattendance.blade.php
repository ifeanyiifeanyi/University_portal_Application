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
            <form action="{{ route('attendance.create') }}" method="POST">
              @csrf
            <table class="table datatable">
              <thead>
                <tr>
                  <th>
                    Sudent name
                  </th>
                  <th>Student department</th>
                  <th>Course name</th>
                 
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
               @forelse ($students as $key => $student)
               <tr>
                <td>
            {{$student->student->user->first_name}} {{$student->student->user->last_name}} {{$student->student->user->other_name}}
                </td>
                <td>{{$student->department->name}}</td>
                <td>{{$student->course->title}}</td>
                <td>
                    <input type="hidden" name="attendance[{{ $key }}][student_id]" value="{{$student->student_id}}">
                    <input type="hidden" name="attendance[{{ $key }}][semester_id]" value="{{$semesterid}}">
                    <input type="hidden" name="attendance[{{ $key }}][course_id]" value="{{$student->course_id}}">
                    <input type="hidden" name="attendance[{{ $key }}][session_id]" value="{{$student->academic_session_id}}">
                    <input type="hidden" name="attendance[{{ $key }}][department_id]" value="{{$student->department_id}}">

                    <input type="checkbox" name="status" value="present" 
                        >
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
            <button type="submit" class="btn w-100 btn-success">Create</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>
@endsection