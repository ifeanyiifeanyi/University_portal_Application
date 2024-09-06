@extends('teacher.layouts.teacher')

@section('title', 'Create attendance')
@section('css')

@endsection



@section('teacher')
{{-- <div class="pagetitle">
    <h1>Courses</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title --> --}}

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Create attendance for all the corses assigned</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>
                    <b>C</b>ourse name
                  </th>
                  <th>Course code</th>
                  <th>Department</th>
                  <th>Semester</th>
                  
                  <th>Session</th>
                  <th>Take attendance</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($coursesassigned as $coursesassigned)
                <tr>
                  <td>{{$coursesassigned->course->title}}</td>
                  <td>{{$coursesassigned->course->code}}</td>
                  <td>{{$coursesassigned->department->name}}</td>
                  <td>{{$coursesassigned->semester->name}}</td>
                  <td>{{$coursesassigned->academicSession->name}}</td>
                  <th><a href="{{route('teacher.create.attendance',['sessionid'=>$coursesassigned->academicSession->id,'semesterid'=>$coursesassigned->semester->id,'departmentid'=>$coursesassigned->department->id,'courseid'=>$coursesassigned->course->id])}}" class="btn w-100 btn-success">Take attendance</a></th>
                </tr>
                @empty
                  
                @endforelse
             
                
              </tbody>
            </table>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>

@endsection