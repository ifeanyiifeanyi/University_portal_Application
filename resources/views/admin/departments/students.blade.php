@extends('admin.layouts.admin')

@section('title', 'Department Students')

@section('admin')
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-12">
                <div class="card">


                        <div class="card-body">
                            <div class="row mb-4">

                                <div class="col-md-6 mb-4">
                                    <a href="{{ route('admin.department.exportCsv', $departmentStudent->id) }}"
                                        class="btn btn-secondary">Export</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Mat No.</th>
                                                <th>Profile</th>
                                                <th>Student</th>
                                                <th>Gender</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @dd($departmentStudent) --}}
                                            @foreach ($departmentStudent->students as $student)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $student->matric_number }}</td>
                                                    <td>
                                                        <img src="{{ $student->user->profileImage() }}"
                                                            class="img-thumbnail img-fluid" width="40" alt="">
                                                    </td>
                                                    <td>{{ $student->user->fullName() }}</td>
                                                    <td>{{ $student->gender }}</td>
                                                    <td>{{ $student->user->email }}</td>
                                                    <td>{{ $student->user->phone }}</td>
                                                    <td>
                                                        {{ $student->current_level }}

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        @endsection
