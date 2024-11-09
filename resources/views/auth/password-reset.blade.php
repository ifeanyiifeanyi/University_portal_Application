@extends('auth.layouts.auth')

@section('title', 'Password Reset')

@section('auth')
    <div class="card">
        <div class="card-body">
            <div class="border p-4 rounded">
                <div class="text-center">
                    <h3 class="">Reset Password</h3>
                </div>

                <x-guest-alert />

                <form class="row g-3" method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="col-12">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" id="new_password"
                            placeholder="Enter new password">
                    </div>
                    <div class="col-12">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control"
                            id="new_password_confirmation" placeholder="Confirm new password">
                    </div>
                    <div class="col-12">
                        <div class="d-grid">
                            <button type="submit" class="btn" style="background: #204939;color:#fff">Reset Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
