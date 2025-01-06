@extends('student.layouts.student')

@section('title', 'Student profile')
@section('student')
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Change password</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Change password</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="d-flex justify-content-center">
            <div class="col-xl-6">

                  <div class="card-body pt-3">
                    @include('messages')
                    <form action="{{ route('student.update.password')}}" method="post">
                        @csrf
                        
                        <div class="card">
                            <div class="card-body">
                                <h5 class="d-flex align-items-center mb-3">Update Password</h5>
                                <div class="form-group mb-3">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control" @error('current_password') autofocus @enderror>
                                    @error('current_password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" name="password_confirmation"
                                        id="password_confirmation" class="form-control"
                                        @error('current_password') autofocus @enderror>
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </div>
                    </form>
      
                  </div>
                
      
              </div>
        </div>
    </div>
</div>
@endsection