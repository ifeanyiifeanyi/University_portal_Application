@extends('errors.layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('message', 'Sorry, you do not have permission to access this resource.')
@section('icon-bg', 'bg-danger bg-opacity-25')
@section('icon')
    <i class="fas fa-ban text-danger fs-1"></i>
@endsection

@section('logout-button')
    <form method="GET" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger error-btn">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
@endsection
