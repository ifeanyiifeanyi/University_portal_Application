@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">School fees</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">School fees</li>
            </ol>
        </div>
    </div>

    <div class="row">
        @include('messages')
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">School fees history</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Invoice #</th>
                                    <th scope="col">Amount ₦</th>
                                    <th scope="col">Level</th>
                                    <th scope="col">Payment Type</th>
                                    <th scope="col">Session</th>
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td>ewwefwe</td>
                                    <td>wefwefw</td>
                                    <td>Full Payment</td>
                                    <td>2018-2019</td>
                                    <td><a href="{{route('student.view.fees')}}" class="btn w-50 text-white" style="background: #AE152D;">View</a></td>
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