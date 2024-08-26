@extends('admin.layouts.admin')

@section('title', 'Pending Accessment Scores')

@section('css')
    <style>
        .modal-backdrop {
            z-index: 1040;
        }

        .modal {
            z-index: 1050;
        }
    </style>
@endsection

@section('admin')
    <div class="container">
        {{-- <h3>@yield('title')</h3> --}}
        <p>
            <button onclick="history.back()" class="btn"
                style="background-color: rgb(81, 0, 128); color:white">Back</button>
        </p>
        @include('admin.alert')
        <div class="card py-3 px-3">
            <div class="row">
                <div class="col-md-7 mx-auto">
                    <h4 class="text-center">Filter Your Result Search</h4>
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
                                <button type="submit" class="btn btn-primary">Filter</button>
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
                            <a href="{{ route('admin.score.export', ['academic_session_id' => $selectedSession, 'semester_id' => $selectedSemester]) }}"
                                class="btn" style="background-color: purple;color:white">Export to CSV</a>
                        </div>
                        <div class="col-md-8">
                            <form style="float: left" action="{{ route('admin.score.import') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="file" name="csv_file" class="form-control mb-2" id="csv_file">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-info">Import CSV</button>

                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <form id="scoreForm" action="{{ route('admin.score.approval.approve') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped" id="example">
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingScores as $score)
                            <tr>
                                <td><input type="checkbox" name="score_ids[]" value="{{ $score->id }}"></td>
                                <th>{{ $loop->iteration }}</th>
                                <td>
                                    {{ $score->student->user->fullName() }} <br>
                                    <span class="text-">{{ $score->student->matric_number }}</span>
                                </td>
                                <td>{{ $score->course->title }}</td>
                                <td>{{ $score->department->name }}</td>
                                <td>{{ $score->teacher->teacher_title }} {{ $score->teacher->user->fullName() }}</td>
                                <td>{{ $score->assessment_score }}</td>
                                <td>{{ $score->exam_score }}</td>
                                <td>{{ $score->total_score }}</td>
                                <td>{{ $score->grade }}</td>
                                <td>
                                    <a style="background: rgb(119, 44, 113)" onclick="return confirm('Are you sure of this action ?')" href="{{ route('admin.score.approval.single.approve', $score->id) }}" class="btn text-light">
                                        Approve Score
                                    </a>

                                    <a style="background: rgb(219, 30, 93)" onclick="return confirm('Are you sure of this action ?')" href="{{ route('admin.score.approval.single.reject', $score->id) }}" class="btn text-light">
                                        Reject Score
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
                <button onclick="return confirmApprove()" type="submit" class="btn btn-success">Approve Selected</button>

                <button onclick="return confirmReject()" type="submit" class="btn btn-danger"
                    formaction="{{ route('admin.score.approval.reject') }}">Reject Selected</button>
            </div>

        </form>

        {{ $pendingScores->links() }}
    </div>



@endsection

@section('javascript')
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.getElementsByName('score_ids[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        function confirmApprove() {
            return confirm('Are you sure you want to approve the selected scores?');
        }

        function confirmReject() {
            return confirm('Are you sure you want to reject the selected scores?');
        }
    </script>
@endsection
