@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('message', 'Whoops, something went wrong on our servers.')
@section('icon-bg', 'bg-danger bg-opacity-25')
@section('icon')
    <i class="fas fa-bug text-danger fs-1"></i>
@endsection
