@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Results</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Results</li>
            </ol>
        </div>
    </div>

    <div class="row">
        @include('messages')
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Results</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Session</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Semester</th>
                                    <th scope="col">CGPA</th>
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td>2018-2019</td>
                                    <td>100 level</td>
                                    <td>First semester</td>
                                    <td>5.00</td>
                                    <td><a href="{{route('student.view.result')}}" class="btn w-50 text-white" style="background: #AE152D;">View</a></td>
                                </tr>
                         
                               
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection