@extends('errors.layout')

@section('title', 'Payment Required')
@section('code', '402')
@section('message', 'Payment is required to access this resource.')
@section('icon-bg', 'bg-secondary bg-opacity-25')
@section('icon')
    <i class="fas fa-credit-card text-secondary fs-1"></i>
@endsection
