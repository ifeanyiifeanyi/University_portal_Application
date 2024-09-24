@extends('teacher.layouts.teacher')

@section('title', 'Teacher Dashboard')
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
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">

          <div class="col-xxl-4 col-xl-12">

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

              </div>
            </div>

          </div>

        

       

         

        

         

        </div>
      </div><!-- End Left side columns -->

    

    </div>
  </section>
@endsection
