@extends('teacher.layouts.teacher')

@section('title', 'Lecturers profile')
@section('css')
<style>
  .profile-card {
      border: none;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  }

  .profile-card .card-header {
      border-radius: 15px 15px 0 0;
  }

  .profile-picture {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border: 5px solid #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  .profile-info p {
      margin-bottom: 0.5rem;
  }

  .profile-info strong {
      min-width: 150px;
      display: inline-block;
  }

  h5 {
      color: #007bff;
      border-bottom: 2px solid #007bff;
      padding-bottom: 5px;
      margin-bottom: 15px;
  }

  ul li {
      margin-bottom: 5px;
  }
  </style>
@endsection



@section('teacher')
@if ($profile)

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
      {{-- <div class="col-xl-5">

        <div class="card">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <img src="{{$getuser->profileimage()}}" alt="Profile" class="rounded-circle">
            <h2>{{$getuser->fullname()}}</h2>
          </div>
        </div>

      </div> --}}

      <div class="col-xl-5">
        <div class="card profile-card">
          <div class="card-header text-white" style="background: #157020;">
              <h3 class="mb-0">{{ $profile->user->first_name }} {{ $profile->user->last_name }} {{ $profile->user->other_name }}</h3>
          </div>
          <div class="card-body">
              <div class="text-center mb-4">
                  <img src="{{ $profile->user->profileimage() }}" alt="Profile Picture" class="rounded-circle profile-picture">
                  <h4 class="mt-3">{{ $profile->teacher_title }} {{ $profile->user->first_name }} {{ $profile->user->last_name }}</h4>
                  <p class="text-muted">{{ $profile->teacher_type }}</p>
              </div>
      
              <div class="profile-info">
                  <p><strong>Email:</strong> {{ $profile->user->email }}</p>
                  <p><strong>Date of Birth:</strong> {{ $profile->date_of_birth }}</p>
                  <p><strong>Gender:</strong> {{ ucfirst($profile->gender) }}</p>
                  <p><strong>Teaching Experience:</strong> {{ $profile->teaching_experience }} years</p>
                  <p><strong>Qualification:</strong> {{ $profile->teacher_qualification }}</p>
                  <p><strong>Date of Employment:</strong> {{ $profile->date_of_employment }}</p>
                  <p><strong>Address:</strong> {{ $profile->address }}</p>
                  <p><strong>Nationality:</strong> {{ $profile->nationality }}</p>
                  <p><strong>Level:</strong> {{ str_replace('_', ' ', $profile->level) }}</p>
                  <p><strong>Office Hours:</strong> {{ $profile->office_hours }}</p>
                  <p><strong>Office Address:</strong> {{ $profile->office_address }}</p>
              </div>
      
              <div class="mt-4">
                  <h5>Biography</h5>
                  <p>{{ $profile->biography }}</p>
              </div>
      
              <div class="mt-4">
                  <h5>Certifications</h5>
                  <ul class="list-unstyled">
                      @foreach(json_decode($profile->certifications) as $certification)
                          <li>{{ $certification }}</li>
                      @endforeach
                  </ul>
              </div>
      
              <div class="mt-4">
                  <h5>Publications</h5>
                  <ul class="list-unstyled">
                      @foreach(json_decode($profile->publications) as $publication)
                          <li>{{ $publication }}</li>
                      @endforeach
                  </ul>
              </div>
      
              <div class="mt-4">
                  <h5>Awards</h5>
                  <p>Number of Awards: {{ $profile->number_of_awards }}</p>
              </div>
          </div>
      </div>
        {{-- <div class="card profile-card">
            <div class="card-header text-white" style="background: #157020;">
                <h3 class="mb-0">{{ $getuser->first_name }} {{ $getuser->last_name }} {{ $getuser->other_name }}</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="{{$getuser->profileimage()}}" alt="Profile Picture" class="rounded-circle profile-picture">
                    <h4 class="mt-3">{{ $profile->teacher_title }} {{ $getuser->first_name }} {{ $getuser->last_name }}</h4>
                    <p class="text-muted">{{ $profile->teacher_type }}</p>
                </div>

                <div class="profile-info">
                    <p><strong>Email:</strong> {{ $getuser->email }}</p>
                    <p><strong>Date of Birth:</strong> {{ $profile->date_of_birth }}</p>
                    <p><strong>Gender:</strong> {{ ucfirst($profile->gender) }}</p>
                    <p><strong>Teaching Experience:</strong> {{ $profile->teaching_experience }} years</p>
                    <p><strong>Qualification:</strong> {{ $profile->teacher_qualification }}</p>
                    <p><strong>Date of Employment:</strong> {{ $profile->date_of_employment }}</p>
                    <p><strong>Address:</strong> {{ $profile->address }}</p>
                    <p><strong>Nationality:</strong> {{ $profile->nationality }}</p>
                    <p><strong>Level:</strong> {{ str_replace('_', ' ', $profile->level) }}</p>
                    <p><strong>Office Hours:</strong> {{ $profile->office_hours }}</p>
                    <p><strong>Office Address:</strong> {{ $profile->office_address }}</p>
                </div>

                <div class="mt-4">
                    <h5>Biography</h5>
                    <p>{{ $profile->biography }}</p>
                </div>

                <div class="mt-4">
                    <h5>Certifications</h5>
                    <ul class="list-unstyled">
                        @foreach(json_decode($profile->certifications) as $certification)
                            <li>{{ $certification }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-4">
                    <h5>Publications</h5>
                    <ul class="list-unstyled">
                        @foreach(json_decode($profile->publications) as $publication)
                            <li>{{ $publication }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-4">
                    <h5>Awards</h5>
                    <p>Number of Awards: {{ $profile->number_of_awards }}</p>
                </div>
            </div>
        </div> --}}
    </div>

      <div class="col-xl-7">

        <div class="card">
          <div class="card-body pt-3">
            @if (!$profile)
                {{-- @include('teacher.profile.createprofile') --}}
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
