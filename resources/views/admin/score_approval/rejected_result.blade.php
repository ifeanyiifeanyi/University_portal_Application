@extends('admin.layouts.admin')

@section('title', 'Rejected Scores')

@section('admin')
    <div class="container">

        @include('admin.alert')
        <div class="card py-3 px-3">
            <div class="row">
                <div class="col-md-7 mx-auto">
                    <h4 class="text-center">Filter Your Result Search</h4>
                    <form action="{{ route('admin.score.rejected.view') }}" method="GET" class="mb-4">
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
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i></button>
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
                            <a href="{{ route('admin.rejected.score.export', ['academic_session_id' => $selectedSession, 'semester_id' => $selectedSemester]) }}"
                                class="btn" style="background-color: rgba(128, 0, 128, 0.527);color:white"><i class="fas fa-file-export"></i> to CSV</a>
                        </div>
                        <div class="col-md-8">
                            <form style="float: left" action="{{ route('admin.rejected.score.import') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="file" name="csv_file" class="form-control mb-2" id="csv_file">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-info text-white"><i class="fas fa-upload"></i> CSV</button>

                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <form id="rejectedScoresForm" action="{{ route('admin.score.approval.rejected.bulk-accept') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="example">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>sn</th>
                            <th>Student</th>
                            <th>Mat ID</th>
                            <th>Course</th>
                            <th>Department</th>
                            <th>Lecturer</th>
                            <th>Assessment</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rejectedScores as $score)
                            <tr>
                                <td><input type="checkbox" name="score_ids[]" value="{{ $score->id }}"></td>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $score->student->user->fullName() }}</td>
                                <td>{{ $score->student->matric_number }}</td>
                                <td>{{ $score->course->title }}</td>
                                <td>{{ $score->department->name }}</td>
                                <td>{{ $score->teacher->teacher_title }} {{ $score->teacher->user->fullName() }}</td>
                                <td>{{ $score->assessment_score }}</td>
                                <td>{{ $score->exam_score }}</td>
                                <td>{{ $score->total_score }}</td>
                                <td>{{ $score->grade }}</td>
                                <td>{{ ucfirst($score->status) }}</td>
                                <td>
                                    <a style="background: rgba(119, 44, 113, 0.596)" onclick="return confirm('Are you sure of this action ?')" href="{{ route('admin.score.approval.rejected.revert', $score->id) }}" class="btn text-light">
                                        <i class="fas fa-history"></i> Revert to pending
                                    </a>
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
                <button style="background-color: rgba(37, 88, 37, 0.685)" onclick="return confirm('Are you sure of this action ?')" type="submit"
                    class="btn text-light"><i class="fas fa-layer-group"></i> Bulk Approve Selected</button>

                <button style="background: rgba(221, 4, 185, 0.623)" onclick="return confirm('Are you sure of this action ?')" type="submit" class="btn text-light"
                    formaction="{{ route('admin.score.approval.rejected.bulk-revert') }}"><i class="fas fa-cubes"></i> Bulk Revert To Pending</button>
            </div>

        </form>

        {{ $rejectedScores->links() }}
    </div>
@endsection
