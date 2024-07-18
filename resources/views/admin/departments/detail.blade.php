@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('css')

@endsection



@section('admin')
<div class="container">
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <h2>{{ Str::title($department->name) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
