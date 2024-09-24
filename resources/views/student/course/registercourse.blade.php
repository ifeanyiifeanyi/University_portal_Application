@extends('student.layouts.student')

@section('title', 'Teacher Dashboard')
@section('student')
<div class="container">
    {{-- <div class="row mt-5">
        <div class="col-md-6"></div>
    <div class="ms-auto col-md-6">
        <a href="#" class="btn w-100 text-white" style="background: #AE152D;" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">Add courses</a>
    </div>
    </div> --}}
    @if ($semesterregistration)
        
    
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Register courses</h5>
            </div><!-- end card header -->

            <div class="card-body">
                @include('messages')
                <form action="{{route('student.proceed.courseregister')}}" method="POST">
                <div class="table-responsive">
                    
                        @csrf
                        <input type="hidden" name="session" value="{{ $session }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <input type="hidden" name="level" value="{{ $level }}">
                        <input type="hidden" name="semesterregid" value="{{$semesterregid}}">
                        <input type="hidden" name="TotalCreditLoadCount" id="TotalCreditLoadCount">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                
                                <th scope="col">Course code</th>
                                <th scope="col">Course name</th>
                                <th scope="col">Credit load</th>
                                <th scope="col"></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                            <tr>
                                
                                <td>{{$course->course->code}}</td>
                                <td>{{$course->course->title}}</td>
                                <td>{{$course->course->credit_hours}} <input type="hidden" name="credit_load" value="{{$course->course->credit_hours}}"></td>
                                <td>
                                    
                                    <input type="checkbox" name="course_id[]" value="{{$course->course->id}}" data-credit-hours="{{ $course->course->credit_hours }}" onchange="updateCreditLoad()" checked>
                                </td>
                             
                            </tr>
                            @endforeach
                            
                           
                        </tbody>
                        
                        
                    </table>
                    <div class="mt-5">
                        <button class="btn w-100 text-white" style="background: #AE152D;">Submit</button>
                      </div>
                    
                </div>
                <div class="mt-3">
                    <strong>Total Credit Load: <span id="totalCreditLoad">0</span></strong>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endif
</div>


<div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Courses for the active semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
               
                        {{-- <input type="hidden" name="session" value="{{ $session }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">
                        <input type="hidden" name="level" value="{{ $level }}">
                        <input type="hidden" name="semesterregid" value="{{$semesterregid}}"> --}}
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                
                                <th scope="col">Course code</th>
                                <th scope="col">Course name</th>
                                <th scope="col">Credit load</th>
                                <th scope="col"></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($semestercourses as $course)
                            <tr>
                                
                                <td>{{$course->course->code}} <input type="hidden" name="" id="coursecode" value="{{$course->course->code}}"></td>
                                <td>{{$course->course->title}} <input type="hidden" name="" id="coursecode" value="{{$course->course->title}}"></td>
                                <td>{{$course->course->credit_hours}}</td>
                                <td>
                                    
                                    {{-- <input type="checkbox" name="course_id[]" value="{{$course->course->id}}" id=""> --}}
                                </td>
                             
                            </tr>
                            @endforeach
                            
                           
                        </tbody>
                        
                        
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Done</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>
    function updateCreditLoad() {
        let totalCreditLoad = 0;
        document.querySelectorAll('input[name="course_id[]"]:checked').forEach((checkbox) => {
            totalCreditLoad += parseInt(checkbox.dataset.creditHours);
        });
        document.getElementById('totalCreditLoad').innerText = totalCreditLoad;
        document.getElementById('TotalCreditLoadCount').value = totalCreditLoad;
    }
    window.addEventListener('DOMContentLoaded', updateCreditLoad);   
</script>

<script>
    // Maximum allowed credit load
    // const maxCreditLoad = 18;

    // function updateCreditLoad() {
    //     let selectedCourses = [];
    //     document.querySelectorAll('input[name="course_id[]"]:checked').forEach((checkbox) => {
    //         selectedCourses.push(checkbox.value);
    //     });

    //     $.ajax({
    //         url: '{{ route("check.credit.load") }}',
    //         method: 'POST',
    //         data: {
    //             _token: '{{ csrf_token() }}',
    //             course_ids: selectedCourses
    //         },
    //         success: function(response) {
    //             document.getElementById('totalCreditLoad').innerText = response.totalCreditLoad;
    //             if (response.exceedsLimit) {
    //                 alert('Total credit load exceeds the maximum allowed limit of ' + maxCreditLoad + ' credit hours.');
    //             }
    //         }
    //     });
    // }

    // window.addEventListener('DOMContentLoaded', updateCreditLoad);
</script>
@endsection