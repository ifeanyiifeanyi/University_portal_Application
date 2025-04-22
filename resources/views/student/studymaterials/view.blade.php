@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Study Materials for courses</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Study Materials for courses</li>
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
                                    <th scope="col">#</th>
                                    <th scope="col">Lecture Name</th>
                                    <th scope="col">Course Name</th>
                                    <th scope="col">Course Code</th>
                                    <th scope="col">Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($studentmaterials as $studentmaterial)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$studentmaterial->lecture_name}}</td>
                  <td>{{$studentmaterial->course->title}}</td>
                  <td>{{$studentmaterial->course->code}}</td>
                 
                  <td><a href="{{route('student.studymaterials.material.view',['id'=>$studentmaterial->id])}}" class="btn w-100 btn-success" style="color: #ffffff">See material</a></td>
                </tr>
                @empty
                  
                @endforelse
              
                                
                         
                               
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection