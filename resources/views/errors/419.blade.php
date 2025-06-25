@extends('errors.layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('message', 'Sorry, your session has expired. Please refresh and try again.')
@section('icon-bg', 'bg-warning bg-opacity-25')
@section('icon')
    <i class="fas fa-hourglass-end text-warning fs-1"></i>
@endsection

@section('logout-button')
    <form method="GET" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-danger error-btn">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
{{ $exception->getTraceAsString() }}

    </form>
@endsection
