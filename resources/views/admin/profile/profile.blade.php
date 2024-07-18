@extends('admin.layouts.admin')

@section('title', 'Admin Profile')
@section('css')

@endsection



@section('admin')

 
    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="{{ $admin->profileImage() }}" alt="Admin" class="rounded-circle p-1 bg-primary"
                                    width="110">
                                <div class="mt-3">
                                    <h4>{{ $admin->fullName() }}</h4>
                                    <p class="text-secondary mb-1">{{ $admin->admin->role }}</p>
                                    <p class="text-muted font-size-sm">{{ Str::lower($admin->email) }}</p>
                                    <p class="text-muted font-size-sm">{{ Str::lower($admin->phone) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <form action="{{ route('admin.update.profile', $admin->slug) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">First Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ old('first_name', $admin->first_name ?? '') }}" />
                                        @error('first_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Last Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', $admin->last_name ?? '') }}" />
                                        @error('last_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Other Names</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="other_name" class="form-control"
                                            value="{{ old('other_name', $admin->other_name ?? '') }}" />
                                        @error('other_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $admin->email ?? '') }}" />
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">phone</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="tel" name="phone" class="form-control"
                                            value="{{ old('phone', $admin->phone ?? '') }}" />
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Username</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="username" class="form-control"
                                            value="{{ old('username', $admin->username ?? '') }}" />
                                        @error('username')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Profile Photo</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input onChange="changeImg(this)" type="file" name="profile_photo"
                                            class="form-control"
                                            value="{{ old('profile_photo', $admin->profile_photo ?? '') }}" />
                                        @error('profile_photo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div class="col-sm-12">
                                            @if ($admin->profile_photo)
                                                <img id="previewImage" src="{{ $admin->profileImage() }}"
                                                    alt="Profile Photo" class="img-fluid rounded"
                                                    style="max-width: 100px" />
                                            @else
                                                <img id="previewImage" src="{{ asset('no_image.jpg') }}"
                                                    alt="Profile Photo" class="img-fluid rounded" width="120" />
                                            @endif
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="submit" class="btn btn-primary px-4 w-100" value="Save Changes" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="{{ route('admin.update.password', $admin->slug) }}" method="post">
                                @csrf
                                @method('PATCH')
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
    </div>

@endsection

@section('javascript')

    <script>
        function changeImg(input) {
            let preview = document.getElementById('previewImage');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
