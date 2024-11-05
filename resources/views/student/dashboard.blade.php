@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')

@php
            // Create a collection from the teacher's attributes and check if any is empty
            $incompleteProfile = collect($student->getAttributes())->except(['deleted_at','cgpa'])->contains(function ($value) {
                return empty($value);
            });
        @endphp
        @if($incompleteProfile)
        <div class="alert alert-warning mt-3">
            Please complete your profile to continue.
            <div class="mt-3">
              <a href="{{route('student.view.profile')}}" style="" class="btn text-white w-50 btn-success">View profile</a>
            </div>
        </div>
       
    @else
        {{-- <div class="alert alert-success">
            Your profile is complete!
        </div> --}}
    @endif
                    <!-- Start Content-->
                    <div class="container-xxl">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="text-muted mb-3 fw-semibold">Total fees paid</p>
                                                <h4 class="m-0 mb-3 fs-18">N {{$totalfees}}</h4>
                                             
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="text-muted mb-3 fw-semibold">Total Cgpa</p>
                                                <h4 class="m-0 mb-3 fs-18">{{$student->cgpa}}</h4>
                                              
                                            </div>

                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                            

                            

                        </div>
                        <div class="row">
                            <div class="col-md-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="text-muted mb-3 fw-semibold">Profile</p>
                                                <div class="mt-3">
                                                    <a href="{{route('student.view.profile')}}" style="" class="btn text-white w-100 btn-success">View profile</a>
                                                  </div>
                                              
                                            </div>

                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="text-muted mb-3 fw-semibold">Virtual Id Card</p>
                                                @if($incompleteProfile)
                                                Complete your profile to view your Id card
                                                @else
                                                <div class="mt-3">
                                                    <a href="{{route('student.view.virtualid')}}" style="" class="btn text-white w-100 btn-success">View student id</a>
                                                  </div>
                                                @endif
                                               
                                              
                                            </div>

                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        


                    </div> <!-- container-fluid -->
@endsection
