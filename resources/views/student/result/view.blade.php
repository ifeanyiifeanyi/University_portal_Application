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
                                    
                                    <th scope="col">Course Code</th>
                                    <th scope="col">Course Title</th>
                                    <th scope="col">Course Unit</th>
                                    <th scope="col">Test Score</th>
                                    <th scope="col">Exam Score</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Grade</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($studentresults as $studentresult)
                                <tr>
                                    <td>{{$studentresult->course->title}}</td>
                                    <td>{{$studentresult->course->code}}</td>
                                    <td>{{$studentresult->course->credit_hours}}</td>
                                    <td>{{$studentresult->assessment_score}}</td>
                                    <td>{{$studentresult->exam_score}}</td>
                                    <td>{{$studentresult->total_score}}</td>
                                    <td>{{$studentresult->grade}}</td>
                                    
                                </tr>   
                                @empty
                                    <p>No result found</p>
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