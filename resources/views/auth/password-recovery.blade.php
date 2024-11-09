@extends('auth.layouts.auth')

@section('title', 'Password Recovery')

@section('auth')
    <div class="card">
        <div class="card-body">
            <div class="border p-4 rounded">
                <div class="text-center">
                    <h3 class="">Password Recovery</h3>
                </div>
                <x-guest-alert />

                <form class="row g-3" method="POST" action="{{ route('password.recovery.send') }}">
                    @csrf
                    <div class="col-12">
                        <label for="recovery_identifier" class="form-label">Email Address</label>
                        <input type="email" name="recovery_identifier" class="form-control" id="recovery_identifier"
                            placeholder="Enter your email address">
                    </div>
                    <div class="col-12">
                        <div class="d-grid">
                            <button type="submit" class="btn" style="background: #204939;color:#fff">Send Recovery
                                Link</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
