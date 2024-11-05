@extends('teacher.layouts.teacher')

@section('title', 'Teacher Departments')
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
            <h5 class="card-title">Courses assigned</h5>
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
                  <th>View students</th>
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
                  <th><a href="{{route('teacher.view.courses.students',['id'=>$coursesassigned->course->id])}}" class="btn w-100 btn-success" style="color: #ffffff">View students</a></th>
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