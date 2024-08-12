@extends('student.layouts.student')

@section('title', 'Student profile')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Profile</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>
    </div>
    @if ($getuser)
        
   
    <div class="row">
        <div class="col-xl-4">
  
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
  
              <img src="{{$getuser->profileImage()}}" alt="Profile" class="rounded-circle">
              <h2>{{$getuser->fullName()}}</h2>
            </div>
          </div>
  
        </div>
  
        <div class="col-xl-8">
        
  
          <div class="card">
            <div class="card-header">
              <h2>Personal details</h2>
            </div>
            <div class="card-body pt-3">
              @include('messages')
                @if (!$student)
                @include('student.profile.createprofile')
                    @else
                @include('student.profile.updateprofile')
                @endif
                
            </div>
          </div>
  
        </div>
      </div>
      @endif
</div> 
@endsection