@extends('student.layouts.student')

@section('title', 'Fees error')
@section('student')
<div class="container">
<div class="row d-flex justify-content-center mt-5">
    <div class="col-lg-9">
    <div class="card">
       

        <div class="card-body">
            <div class="text-center mt-2">
                Our records indicate that your school fees for the current session and semester have not yet been paid. Kindly proceed with the necessary payments to avoid any disruptions to your academic activities
            </div>
            <div class="mt-5"><a href="{{route('student.view.fees.all')}}" class="btn w-100 text-white" style="background: #AE152D;">Go to fees</a></div>
        </div>
    </div>
    </div>
</div>
</div>
@endsection