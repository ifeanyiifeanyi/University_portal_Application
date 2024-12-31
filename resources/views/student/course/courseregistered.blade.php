@extends('student.layouts.student')

@section('title', 'Course Registration (Student Dashboard)')
@section('student')
    <div class="container-xxl mt-3">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Courses registered</h4>
            </div>


        </div>
        <div class="row">
        </div>
        <div class="row">
            @include('messages')
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style=" color: #ffffff;">
                        <h5 class="card-title mb-0 text-white">Registered Courses</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Course code</th>
                                    <th scope="col">Course title</th>
                                    <th scope="col">Course unit</th> 
                                    <th scope="col">Course type</th>
                                    <th scope="col">Course status</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registered as $enrollment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $enrollment->course?->code }}</td>
                                        <td>{{ $enrollment->course?->title }}</td>
                                        <td>{{ $enrollment->course?->credit_hours }}</td>
                                        <td>{{ $enrollment->course?->course_type }}</td>
                                        <td>{{ $enrollment->semesterCourseRegistration?->status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No courses registered</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
