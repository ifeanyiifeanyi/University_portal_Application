@extends('student.layouts.student')

@section('title', 'Online classes')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Online classes</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Online classes</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Online classes list</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th scope="col">Course name</th>
                                    <th scope="col">Course code</th>
                                    <th scope="col">Lecturer</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <td>Computer nae</td>
                                <td>CIS 101</td>
                                <td>Nepak ejio</td>
                                <td>Ongoing</td>
                                <td>
                                    <a href="#" class="btn w-50 text-white" style="background: #AE152D;">Open</a>
                                </td>
                                </tr>
                                <tr>
                                    <td>Computer nae</td>
                                    <td>CIS 101</td>
                                    <td>Nepak ejio</td>
                                    <td>Ended</td>
                                    <td><a href="#" class="btn w-50 text-white" style="background: #AE152D;">Open</a></td>
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