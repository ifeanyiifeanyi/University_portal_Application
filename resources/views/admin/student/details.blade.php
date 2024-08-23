@extends('admin.layouts.admin')

@section('title', 'Student Details')

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
                        <h4 class="card-title mb-4">Student Details</h4>

                        <div class="student-profile py-4">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-transparent text-center">
                                                <img class="profile_img"
                                                    src="{{ asset($student->user->profile_photo ?? 'no_image.jpg') }}"
                                                    alt="Student Profile Picture">
                                                <h3>{{ $student->user->first_name }} {{ $student->user->last_name }}</h3>
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-0"><strong>Matric Number:</strong> <br>
                                                    {{ $student->matric_number }}</p> <hr>
                                                <p class="mb-0"><strong>Department:</strong> <br>
                                                    {{ $student->department->name }}</p> <hr>
                                                <p class="mb-0"><strong>Current Level:</strong> <br>
                                                    {{ $student->current_level }}</p> <hr>
                                                <p class="mb-0"><strong>Year of Admission:</strong>
                                                    {{ $student->year_of_admission }}</p> <hr>
                                                <p class="mb-0"><strong>Mode of Entry:</strong> <br>
                                                    {{ $student->mode_of_entry }}</p>
                                            </div>
                                            <a href="{{ route('admin.students.registration-history', $student->id) }}" class="btn btn-info mb-3">View Registered courses History</a>

                                            <a href="{{ route('admin.student.approved-score-history', $student->id) }}" class="btn btn-primary">View Approved Score History</a>
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
                                                            <td>{{ $student->user->first_name }}
                                                                {{ $student->user->last_name }}
                                                                {{ $student->user->other_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>:</td>
                                                            <td>{{ $student->user->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>:</td>
                                                            <td>{{ $student->user->phone }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date of Birth</th>
                                                            <td>:</td>
                                                            <td>{{ $student->date_of_birth }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Gender</th>
                                                            <td>:</td>
                                                            <td>{{ $student->gender }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Marital Status</th>
                                                            <td>:</td>
                                                            <td>{{ $student->marital_status }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Religion</th>
                                                            <td>:</td>
                                                            <td>{{ $student->religion }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card shadow-sm mt-3">
                                            <div class="card-header bg-transparent border-0">
                                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Origin Information</h3>
                                            </div>
                                            <div class="card-body pt-0">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">Nationality</th>
                                                        <td width="2%">:</td>
                                                        <td>{{ $student->nationality }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>State of Origin</th>
                                                        <td>:</td>
                                                        <td>{{ $student->state_of_origin }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>LGA of Origin</th>
                                                        <td>:</td>
                                                        <td>{{ $student->lga_of_origin }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Hometown</th>
                                                        <td>:</td>
                                                        <td>{{ $student->hometown }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card shadow-sm mt-3">
                                            <div class="card-header bg-transparent border-0">
                                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Address Information</h3>
                                            </div>
                                            <div class="card-body pt-0">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">Residential Address</th>
                                                        <td width="2%">:</td>
                                                        <td>{{ $student->residential_address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Permanent Address</th>
                                                        <td>:</td>
                                                        <td>{{ $student->permanent_address }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card shadow-sm mt-3">
                                            <div class="card-header bg-transparent border-0">
                                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Medical Information</h3>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Blood Group</th>
                                                            <td width="2%">:</td>
                                                            <td>{{ $student->blood_group }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Genotype</th>
                                                            <td>:</td>
                                                            <td>{{ $student->genotype }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card shadow-sm mt-3">
                                            <div class="card-header bg-transparent border-0">
                                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Next of Kin Information
                                                </h3>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="table-responsive">
                                                    <div class="table-responsive">

                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th width="30%">Name</th>
                                                                <td width="2%">:</td>
                                                                <td>{{ $student->next_of_kin_name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Relationship</th>
                                                                <td>:</td>
                                                                <td>{{ $student->next_of_kin_relationship }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Phone</th>
                                                                <td>:</td>
                                                                <td>{{ $student->next_of_kin_phone }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Address</th>
                                                                <td>:</td>
                                                                <td>{{ $student->next_of_kin_address }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card shadow-sm mt-3">
                                            <div class="card-header bg-transparent border-0">
                                                <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Academic Information
                                                </h3>
                                            </div>
                                            <div class="card-body pt-0">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="30%">JAMB Reg. Number</th>
                                                        <td width="2%">:</td>
                                                        <td>{{ $student->jamb_registration_number }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.student.edit', $student->id) }}" class="btn btn-primary">Edit
                                Student</a>
                            <a href="{{ route('admin.student.view') }}" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // Any additional JavaScript can be added here
    </script>
@endsection
