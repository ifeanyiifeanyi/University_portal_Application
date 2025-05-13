@extends('errors.layout')

@section('title', 'Unauthorized')
@section('code', '401')
@section('message', 'Sorry, you are not authorized to access this resource.')
@section('icon-bg', 'bg-danger bg-opacity-25')
@section('icon')
    <i class="fas fa-lock text-danger fs-1"></i>
@endsection

@section('logout-button')
    <form method="GET" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-danger error-btn">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
@endsection
