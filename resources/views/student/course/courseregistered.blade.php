@extends('student.layouts.student')

@section('title', 'Teacher Dashboard')
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
                                        <th scope="col">Course code</th>
                                        <th scope="col">Course title</th>
                                        <th scope="col">Course unit</th>
                                        <th scope="col">Course type</th>
                                        <th scope="col">Course status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($registered as $registered)
                                        <tr>
                                            <td>{{ $registered->course->code }}</td>
                                            <td>{{ $registered->course->title }}</td>
                                            <td>{{ $registered->course->credit_hours }}</td>
                                            <td>Compulsory</td>
                                            <td>{{ $registered->semesterCourseRegistration->status }}</td>
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
