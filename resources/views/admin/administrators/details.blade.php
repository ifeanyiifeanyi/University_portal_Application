@extends('admin.layouts.admin')

@section('title', 'Profile')
@section('css')
    <style>
        .student-profile {
            padding: 3rem 0;
        }

        .student-profile .card {
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .student-profile .card .card-header .profile_img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 10px auto;
            border: 10px solid #ccc;
            border-radius: 50%;
        }

        .student-profile .card h3 {
            font-size: 20px;
            font-weight: 700;
        }

        .student-profile .card p {
            font-size: 16px;
            color: #000;
        }

        .student-profile .table th,
        .student-profile .table td {
            font-size: 14px;
            padding: 5px 10px;
            color: #000;
        }

        .student-profile .btn-primary {
            background-color: #14A44D;
            border-color: #14A44D;
        }

        @media (max-width: 767px) {
            .student-profile .card-body {
                padding: 15px;
            }

            .student-profile .card-body .table td,
            .student-profile .card-body .table th {
                padding: 5px;
            }
        }
    </style>
@endsection

@section('admin')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Administrator Details</h4>

                        <div class="student-profile py-4">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-transparent text-center">
                                                <img class="profile_img"
                                                    src="{{ asset($admin->user->profile_photo ?? 'no_image.jpg') }}"
                                                    alt="Admin Profile Picture">
                                                <h3>{{ $admin->user->fullName() }} </h3>
                                            </div>
                                            {{-- @dd($admin->user->user_access_type) --}}
                                            <div class="card-body">
                                                <p class="mb-0"><strong>Role:</strong> <br>
                                                    {{ Str::upper($admin->user->user_access_type) }}/{{ $admin->admin_user_role }}</p>
                                                <hr>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                         <div class="card shadow-sm">
                                            <div class="card-header bg-transparent border-0">
                                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Personal Information
                                                </h3>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Full Name</th>
                                                            <td width="2%">:</td>
                                                            <td>{{ $admin->user->first_name }}
                                                                {{ $admin->user->last_name }}
                                                                {{ $admin->user->other_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>:</td>
                                                            <td>{{ $admin->user->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>:</td>
                                                            <td>{{ $admin->user->phone }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Username</th>
                                                            <td>:</td>
                                                            <td>{{ $admin->user->username }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Login access type</th>
                                                            <td>:</td>
                                                            <td>{{ $admin->user->user_access_type }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Role</th>
                                                            <td>:</td>
                                                            <td>{{ $admin->admin_user_role }}</td>
                                                        </tr>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="" class="btn btn-primary">Edit Student</a>
                            <button onclick="history.back()" class="btn btn-secondary">Back to List</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('javascript')

@endsection
