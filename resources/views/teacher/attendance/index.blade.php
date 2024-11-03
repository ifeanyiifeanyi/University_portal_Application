@extends('teacher.layouts.teacher')

@section('title', 'Teacher Attendance')
@section('css')

@endsection



@section('teacher')
<div class="row mt-5">
        <div class="col-md-6"></div>
    <div class="ms-auto col-md-6 mb-3">
        <a href="{{route('teacher.view.create.attendance')}}" class="btn w-100" style="background: #AE152D; color: #ffffff">Create new attendance</a>
    </div>
    </div>
<section class="section">

    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Attendance created</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>
                    Session
                  </th>
                  <th>Semester</th>
                  <th>Department</th>
                  <th>Course name</th>
                  <th>Date of lecture</th>
                  <th>Attendees</th>
                  <th>View</th>
                </tr>
              </thead>
              <tbody>
               @foreach ($attendances as $attendance)
               <tr>
                <td>{{$attendance['session']}}</td>
                <td>{{$attendance['semester']}}</td>
                <td>{{$attendance['department']}}</td>
                <td>{{$attendance['course']}}</td>
                <td>{{ \Carbon\Carbon::parse($attendance['date'])->format('d F, Y') }}</td>
                <td>1</td>
                <th><a href="{{route('teacher.view.attendees',['attendanceid'=>$attendance['attendanceid'],'departmentid'=>$attendance['departmentid'],'courseid'=>$attendance['courseid']])}}" class="btn w-100" style="background: #AE152D; color: #ffffff">View attendees</a></th>
              </tr>
              @endforeach
               
             
             
                
              </tbody>
            </table>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>
@endsection