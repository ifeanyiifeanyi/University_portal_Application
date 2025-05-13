@extends('auth.layouts.auth')

@section('title', 'Password Recovery')

@section('auth')
    <div class="text-center mb-4">
        <h2 class="auth-title">Password Recovery</h2>
        <p class="text-muted">Enter your email to receive a recovery link</p>
    </div>

    <x-guest-alert />

    <form class="auth-form" method="POST" action="{{ route('password.recovery.send') }}">
        @csrf

        <div class="mb-4 fade-in-up delay-1">
            <label for="recovery_identifier" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" name="recovery_identifier" class="form-control" id="recovery_identifier"
                    placeholder="name@example.com" value="{{ old('recovery_identifier') }}">
            </div>
            @error('recovery_identifier')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="d-grid mb-4 fade-in-up delay-2">
            <button type="submit" class="auth-btn">
                <i class="fas fa-paper-plane me-2"></i>Send Recovery Link
            </button>
        </div>
    </form>

    <div class="auth-divider fade-in-up delay-3">
        <span>or</span>
    </div>

    <div class="text-center fade-in-up delay-3">
        <a href="{{ route('login.view') }}" class="auth-link">
            <i class="fas fa-arrow-left me-1"></i> Back to Login
        </a>
    </div>
@endsection
