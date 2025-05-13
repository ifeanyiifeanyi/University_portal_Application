@extends('auth.layouts.auth')

@section('title', 'Password Reset')

@section('auth')
    <div class="text-center mb-4">
        <h2 class="auth-title">Reset Password</h2>
        <p class="text-muted">Create a new secure password</p>
    </div>

    <x-guest-alert />

    <form class="auth-form" method="POST" action="{{ route('password.reset') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-3 fade-in-up delay-1">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-group" id="show_hide_password">
                <span class="input-group-text bg-transparent">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="••••••••">
                <a href="javascript:;" class="input-group-text bg-transparent toggle-password" data-target="new_password">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
            @error('new_password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4 fade-in-up delay-2">
            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
            <div class="input-group" id="show_hide_password_confirmation">
                <span class="input-group-text bg-transparent">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation"
                    placeholder="••••••••">
                <a href="javascript:;" class="input-group-text bg-transparent toggle-password" data-target="new_password_confirmation">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
            @error('new_password_confirmation')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="password-strength mb-4 fade-in-up delay-2">
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: 0%;" id="password-strength-bar"></div>
            </div>
            <small class="text-muted mt-1" id="password-strength-text">Password strength</small>
        </div>

        <div class="d-grid mb-4 fade-in-up delay-3">
            <button type="submit" class="auth-btn">
                <i class="fas fa-key me-2"></i>Reset Password
            </button>
        </div>
    </form>

    <div class="text-center fade-in-up delay-4">
        <a href="{{ route('login.view') }}" class="auth-link">
            <i class="fas fa-arrow-left me-1"></i> Back to Login
        </a>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Show/hide password for both password fields
            $('.toggle-password').each(function() {
                $(this).on('click', function(event) {
                    event.preventDefault();
                    var targetId = $(this).data('target');
                    var $input = $('#' + targetId);
                    var $icon = $(this).find('i');

                    if ($input.attr('type') === 'password') {
                        $input.attr('type', 'text');
                        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        $input.attr('type', 'text', 'password');
                        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });
            });

            // Password strength meter
            $('#new_password').on('input', function() {
                var password = $(this).val();
                var strength = 0;

                if (password.length >= 8) strength += 25;
                if (password.match(/[A-Z]/)) strength += 25;
                if (password.match(/[0-9]/)) strength += 25;
                if (password.match(/[^A-Za-z0-9]/)) strength += 25;

                var bar = $('#password-strength-bar');
                var text = $('#password-strength-text');

                bar.css('width', strength + '%');

                if (strength < 25) {
                    bar.removeClass().addClass('progress-bar bg-danger');
                    text.html('Very Weak');
                } else if (strength < 50) {
                    bar.removeClass().addClass('progress-bar bg-warning');
                    text.html('Weak');
                } else if (strength < 75) {
                    bar.removeClass().addClass('progress-bar bg-info');
                    text.html('Good');
                } else {
                    bar.removeClass().addClass('progress-bar bg-success');
                    text.html('Strong');
                }
            });
        });
    </script>
@endsection
