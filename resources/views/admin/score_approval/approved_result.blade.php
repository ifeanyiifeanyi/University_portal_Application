@extends('admin.layouts.admin')

@section('title', 'Approved Results')
@section('css')

@endsection



@section('admin')
    <div class="container-fluid">
        <h2>Approved Scores</h2>

        @include('admin.alert')
        <div class="card py-3 px-3">
            <div class="row">
                <div class="col-md-7 mx-auto">
                    <h4 class="text-center">Filter Approved Scores</h4>
                    <form action="{{ route('admin.score.approval.view') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="department_id" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="academic_session_id" class="form-control">
                                    @foreach ($academicSessions as $session)
                                        <option value="{{ $session->id }}"
                                            {{ $selectedSession == $session->id || ($currentAcademicSession && $session->id == $currentAcademicSession->id) ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>   
                            </div>
                            <div class="col-md-3">
                                <select name="semester_id" class="form-control">
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ $selectedSemester == $semester->id || ($currentSemester && $semester->id == $currentSemester->id) ? 'selected' : '' }}>
                                            {{ $semester->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8 mt-3 mx-auto">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin.score.approval.approved.export', ['academic_session_id' => $selectedSession, 'semester_id' => $selectedSemester]) }}"
                                class="btn btn-sm" style="background-color: rgba(128, 0, 128, 0.575);color:white"><i class="fas fa-file-export"></i> CSV</a>
                        </div>

                        <div class="col-md-8">
                            <form style="float: left" action="{{ route('admin.score.approval.approved.import') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="file" name="excel_file" class="form-control mb-2"
                                                id="excel_file">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-info btn-sm bg-opacity-50 text-light"><i class="fas fa-file-import"></i> CSV</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form id="approvedScoresForm" action="{{ route('admin.score.approval.approved.bulk-revert') }}" method="POST">
                @csrf
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-bordered" id="example">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>sn</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Department</th>
                                <th>Lecturer</th>
                                <th>Assessment</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                {{-- <th>Status</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvedScores as $score)
                                <tr>
                                    <td><input type="checkbox" name="score_ids[]" value="{{ $score->id }}"></td>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>
                                        {{ $score->student->user->fullName() }}
                                        <p class="sm">{{ $score->student->matric_number }}</p>
                                    </td>
                                    <td>{{ $score->course->title }}</td>
                                    <td>{{ $score->department->name }}</td>
                                    <td>{{ $score->teacher->teacher_title }} {{ $score->teacher->user->fullName() }}</td>
                                    <td>{{ $score->assessment_score }}</td>
                                    <td>{{ $score->exam_score }}</td>
                                    <td>{{ $score->total_score }}</td>
                                    <td>{{ $score->grade }}</td>
                                    {{-- <td>{{ ucfirst($score->status) }}</td> --}}
                                    <td>
                                        <a onclick="return confirm('Are you sure of this action ?')" href="{{ route('admin.score.approval.approved.revert', $score) }}" class="btn btn-sm btn-danger">Revert</a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="form-group mt-3">
                    <label for="comment">Comment:</label>
                    <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
                </div>

                <div class="mt-3">
                    <button onclick="return confirm('Are you sure of this action ?')" type="submit"
                        class="btn btn-danger btn-sm bg-opacity-75"><i class="fas fa-history"></i> Revert Selected</button>
                </div>
            </form>


            {{ $approvedScores->links() }}
        </div>
    </div>

@endsection

@section('javascript')

@endsection
