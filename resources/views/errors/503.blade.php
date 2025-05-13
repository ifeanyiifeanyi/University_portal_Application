@extends('errors.layout')

@section('title', 'Service Unavailable')
@section('code', '503')
@section('message', 'Sorry, we are doing some maintenance. Please check back soon.')
@section('icon-bg', 'bg-info bg-opacity-25')
@section('icon')
    <i class="fas fa-wrench text-info fs-1"></i>
@endsection
