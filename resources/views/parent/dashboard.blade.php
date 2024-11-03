@extends('parent.layouts.parent')

@section('title', 'Parent Dashboard')
@section('parent')
<div class="container-xxl">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <p class="text-muted mb-3 fw-semibold">Total children</p>
                            
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2">{{$totalchildrens}}</span>
                            </p>
                        </div>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection