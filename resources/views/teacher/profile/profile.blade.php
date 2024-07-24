@extends('teacher.layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('css')

@endsection



@section('teacher')
@if ($getuser)
    
<div class="pagetitle">
    <h1>Profile</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">Profile</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section profile">
    <div class="row">
      <div class="col-xl-4">

        <div class="card">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <img src="{{$getuser->profileimage()}}" alt="Profile" class="rounded-circle">
            <h2>{{$getuser->fullname()}}</h2>
          </div>
        </div>

      </div>

      <div class="col-xl-8">

        <div class="card">
          <div class="card-body pt-3">
            @if (!$profile)
                @include('teacher.profile.createprofile')
            @else
                @include('teacher.profile.updateprofile')
            @endif
          </div>
        </div>

      </div>
    </div>
  </section>
  @endif
@endsection