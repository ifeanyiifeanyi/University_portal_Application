@extends('errors.layout')

@section('title', 'Not Found')
@section('code', '404')
@section('message', 'Sorry, the page you are looking for could not be found.')
@section('icon-bg', 'bg-warning bg-opacity-25')
@section('icon')
    <i class="fas fa-search text-warning fs-1"></i>
@endsection
