@extends('auth.layouts.auth')

@section('title', 'Login')

@section('css')

@endsection

@section('auth')

    <div class="card">
        <div class="card-body">
            <div class="border p-4 rounded">
                <div class="text-center">
                    <h3 class="">Sign in</h3>

                </div>
                @if (isset($error))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

                @endif

                <div class="form-body">
                    <form class="row g-3" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="col-12">
                            <label for="inputEmailAddress" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" id="inputEmailAddress"
                                placeholder="Email Address">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="inputChoosePassword" class="form-label">Enter
                                Password</label>
                            <div class="input-group" id="show_hide_password">
                                <input type="password" class="form-control border-end-0" id="inputChoosePassword"
                                    value="" name="password" placeholder="Enter Password"> <a href="javascript:;"
                                    class="input-group-text bg-transparent"><i class='fas fa-eye'></i></a>

                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="remember"
                                    checked>
                                <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-md-6 text-end"> <a href="/">Forgot Password ?</a>
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bx bxs-lock-open"></i>Sign
                                    in</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


@endsection
@section('js')

@endsection
