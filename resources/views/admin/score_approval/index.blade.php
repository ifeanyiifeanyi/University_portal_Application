@extends('admin.layouts.admin')

@section('title', 'Pending Accessment Scores')

@section('css')

    <style>
        .filter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .filter-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
        }

        .filter-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .filter-title::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #3498db, #2980b9);
            border-radius: 2px;
        }

        .custom-select {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }

        .custom-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
        }

        .action-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .csv-export-btn {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
            border: none;
        }

        .csv-export-btn:hover {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            transform: translateY(-2px);
            color: white;
        }

        .csv-import-section {
            background: white;
            border-radius: 6px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .file-input-wrapper {
            position: relative;
            margin-bottom: 0;
        }

        .import-btn {
            background: linear-gradient(135deg, #00b894, #00a885);
            border: none;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .import-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #00a885, #00b894);
        }

        hr.styled-hr {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #e0e0e0, transparent);
            margin: 2rem 0;
        }
    </style>
@endsection

@section('admin')
    <div class="container">

        @include('admin.alert')


        <div class="filter-card py-4 px-4">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <h4 class="filter-title text-center">Filter Your Result Search</h4>
                    <form action="{{ route('admin.score.approval.view') }}" method="GET" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <select name="department_id" class="form-control custom-select">
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
                                <select name="academic_session_id" class="form-control custom-select">
                                    @foreach ($academicSessions as $session)
                                        <option value="{{ $session->id }}"
                                            {{ $selectedSession == $session->id || ($currentAcademicSession && $session->id == $currentAcademicSession->id) ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="semester_id" class="form-control custom-select">
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ $selectedSemester == $semester->id || ($currentSemester && $semester->id == $currentSemester->id) ? 'selected' : '' }}>
                                            {{ $semester->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary filter-btn w-100">
                                    <i class="fas fa-filter me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="styled-hr">

            <div class="action-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="{{ route('admin.score.export', ['academic_session_id' => $selectedSession, 'semester_id' => $selectedSemester]) }}"
                            class="csv-export-btn btn">
                            <i class="fas fa-file-download me-2"></i> Export CSV
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('admin.score.import') }}" method="POST" enctype="multipart/form-data"
                            class="csv-import-section">
                            @csrf
                            <div class="row align-items-center g-2">
                                <div class="col-md-8">
                                    <div class="file-input-wrapper">
                                        <input type="file" name="csv_file" class="form-control" id="csv_file">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn import-btn text-white w-100">
                                        <i class="fas fa-file-import me-2"></i> Import
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <form id="scoreForm" action="{{ route('admin.score.approval.approve') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table" id="example">
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
                                <td>{{ $score->teacher->title_and_full_name }}</td>
                                <td>{{ $score->assessment_score }}</td>
                                <td>{{ $score->exam_score }}</td>
                                <td>{{ $score->total_score }}</td>
                                <td>{{ $score->grade }}</td>
                                <td>
                                    <a onclick="handleSingleApproval('{{ route('admin.score.approval.single.approve', $score->id) }}')"
                                        href="javascript:void(0)" class="btn text-light">
                                        <i class="fas fa-check-square text-primary"></i> Approve Score
                                    </a>

                                    <a onclick="handleSingleRejection('{{ route('admin.score.approval.single.reject', $score->id) }}')"
                                        href="javascript:void(0)" class="btn text-light">
                                        <i class="fas fa-window-close text-danger"></i> Reject Score
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
                <button type="button" onclick="confirmApprove()" class="btn btn-success">
                    <i class="fas fa-check-square"></i> Approve Selected
                </button>

                <button type="button" onclick="confirmReject()" class="btn btn-danger">
                    <i class="fas fa-window-close"></i> Reject Selected
                </button>
            </div>

        </form>

        {{ $pendingScores->links() }}
    </div>



@endsection

{{-- @section('javascript')
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
--}}
@section('javascript')
    <script>
        // Handle select all checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.getElementsByName('score_ids[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        // Handle bulk score approval
        function confirmApprove() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to approve all selected scores?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('scoreForm');
                    form.action = "{{ route('admin.score.approval.approve') }}";
                    form.submit();
                }
            });
        }

        // Handle bulk score rejection
        function confirmReject() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to reject all selected scores?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('scoreForm');
                    form.action = "{{ route('admin.score.approval.reject') }}";
                    form.submit();
                }
            });
        }

        // Handle single score approval
        function handleSingleApproval(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to approve this score?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        // Handle single score rejection
        function handleSingleRejection(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to reject this score?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
@endsection
