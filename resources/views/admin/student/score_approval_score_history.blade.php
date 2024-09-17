@extends('admin.layouts.admin')
@section('title', 'Student Score History')
@section('admin')
    <div class="container">
        <p>
            <button onclick="history.back()" class="btn" style="background-color: rgb(81, 0, 128); color:white">
                Back
            </button>
        </p>
        <h4>Result History <code>{{ $student->user->fullName() }}</code></h4>

        <div class="card py-3 px-3">
            @foreach ($scores as $sessionSemester => $sessionScores)
                <h5 class="mb-3">{{ $sessionSemester }}</h5>
                <div class="table-responsive mb-4">
                    <table id="example" class="table table-bordered table-striped border-dark">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Assessment</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessionScores as $score)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $score->course->title }}</td>
                                    <td>{{ $score->teacher->teacher_title }} {{ $score->teacher->user->fullName() }}</td>
                                    <td>{{ $score->assessment_score }}</td>
                                    <td>{{ $score->exam_score }}</td>
                                    <td>{{ $score->total_score }}</td>
                                    <td>{{ $score->grade }}</td>
                                    <td>{{ ucfirst($score->status) }}</td>
                                    <td>
                                        @if ($score->is_failed)
                                        <p class="badge bg-danger">FAILED</p>
                                        @else
                                        <p class="badge bg-primary">PASSED</p>

                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
@endsection
