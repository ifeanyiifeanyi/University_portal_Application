@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
                    <!-- Start Content-->
                    <div class="container-xxl">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <p class="text-muted mb-3 fw-semibold">Total School fees paid</p>
                                                <h4 class="m-0 mb-3 fs-18">N 1,000,000</h4>
                                             
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-3">
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

                        


                    </div> <!-- container-fluid -->
@endsection
