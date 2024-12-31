@extends('student.layouts.student')

@section('title', 'Fees error')
@section('student')
<div class="container">
<div class="row d-flex justify-content-center mt-5">
    <div class="col-lg-9">
    <div class="card">
       

        <div class="card-body">
            <div class="text-center mt-2">
                {{$message}}
            </div>
            <div class="mt-5"><a href="{{route('student.view.fees.all')}}" class="btn w-100 text-white btn-success">Go to fees</a></div>
        </div>
    </div>
    </div>
</div>
</div>
@endsection