@extends('errors.layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('message', 'Sorry, you are making too many requests to our servers.')
@section('icon-bg', 'bg-warning bg-opacity-25')
@section('icon')
    <i class="fas fa-exclamation-triangle text-warning fs-1"></i>
@endsection
