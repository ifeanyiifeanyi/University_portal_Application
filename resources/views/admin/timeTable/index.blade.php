@extends('admin.layouts.admin')

@section('title', 'Time Table Manager')
@section('css')

@endsection



@section('admin')
    <div class="row">
        <a href="{{ route('admin.timetable.create') }}" class="btn btn-primary">create</a>
    </div>
@endsection

@section('javascript')

@endsection
