@extends('admin.layouts.admin')

@section('title', 'Create Administrator Account')
@section('css')
    <style>
        #image-label {
            background: linear-gradient(135deg, #9faef2e5 0%, #b477f2de 100%);
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            left: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            color: white;
            border-radius: 8px;
        }

        #profile_photo {
            display: none;
        }

        .image-preview {
            width: 100%;
            max-width: 150px;
            margin-top: 10px;
            z-index: 70;
        }
    </style>
@endsection



@section('admin')
    <div class="container">
        <button onclick="history.back()" class="btn btn-secondary mb-2"> <i class="fadeIn animated bx bx-chevrons-left"></i> Back to List</button>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.accounts.managers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" class="form-control" id="first_name"
                                    value="{{ old('first_name') }}">
                                @error('first_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="other_name">Other Names</label>
                                <input type="text" name="other_name" class="form-control" id="other_name"
                                    value="{{ old('other_name') }}">
                                @error('other_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" class="form-control" id="email"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Default Password</label>
                                <input type="text" name="password" class="form-control" id="password"
                                    value="12345678">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" class="form-control" id="last_name"
                                    value="{{ old('last_name') }}">
                                @error('last_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="phone">Phone Number</label>
                                <input type="text" name="phone" class="form-control" id="phone"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="role">Administrator Role</label>
                                <select name="role" class="form-control" id="role">
                                    <option value="" disabled selected>Select Administrator Role</option>
                                    @forelse ($roles as $role)
                                        <option {{ old('role') ? 'selected' : '' }} value="{{ $role }}">
                                            {{ $role }}</option>
                                    @empty
                                        <option value=""> loading ...</option>
                                    @endforelse
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="card-body" style="position: relative">
                                    <label for="profile_photo" id="image-label">Click to add Photo</label>
                                    <input id="profile_photo" type="file" name="profile_photo"
                                        accept=".jpg, .png, image/jpeg, image/png" style="display:none;">
                                    <img id="image-preview" src="{{ asset('no_image.jpg') }}" alt="Profile Photo"
                                        class="image-preview">
                                </div>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profilePhotoInput = document.getElementById('profile_photo');
            const imageLabel = document.getElementById('image-label');
            const imagePreview = document.getElementById('image-preview');

            imageLabel.addEventListener('click', function() {
                profilePhotoInput.click();
            });

            profilePhotoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
