@extends('teacher.layouts.teacher')

@section('title', 'Lecturers Dashboard')
@section('css')

@endsection



@section('teacher')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">

    {{-- @if(empty($teacher->date_of_birth) || 
            empty($teacher->gender) || 
            empty($teacher->teaching_experience) || 
            empty($teacher->teacher_type) || 
            empty($teacher->teacher_qualification) || 
            empty($teacher->teacher_title) || 
            empty($teacher->employment_id) || 
            empty($teacher->date_of_employment) || 
            empty($teacher->address) || 
            empty($teacher->nationality) || 
            empty($teacher->level))
            <div class="alert alert-warning">
                Please complete your profile to continue.
                <div class="mt-3">
                  <a href="{{route('teacher.view.profile')}}" style="background: #AE152D;" class="btn text-white w-50">View profile</a>
                </div>
            </div>
        @else
         
        @endif --}}
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <div class="col-xxl-4 col-xl-6">

            <div class="card info-card customers-card">

              

              <div class="card-body">
                <h5 class="card-title">Courses assigned</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{$coursesassignedcount}}</h6>
                   

                  </div>
                </div>
                <a href="{{route('teacher.view.courses')}}" style="background: #AE152D;" class="btn text-white mt-3 w-100">View courses</a>

              </div>
            </div>

          </div>


          <div class="col-xxl-4 col-xl-6">

            <div class="card info-card customers-card">

              

              <div class="card-body">
                <h5 class="card-title">View profile</h5>

                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-profile"></i>
                  </div>
                  <div class="ps-3">
                    <h6>Navigate to profile</h6>
                  </div>
                </div>
                <a href="{{route('teacher.view.profile')}}" style="background: #AE152D;" class="btn text-white mt-3 w-100">View profile</a>

              </div>
            </div>

          </div>

        

       

         

        

         

        </div>
      </div><!-- End Left side columns -->

    

    </div>
  </section>
@endsection
