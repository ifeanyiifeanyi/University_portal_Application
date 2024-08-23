@extends('admin.layouts.admin')
@section('title', 'Student Score History')
@section('admin')
    <div class="container">
        <h2>Score History for {{ $student->user->fullName() }}</h2>
        <p>
            <button onclick="history.back()" class="btn" style="background-color: rgb(81, 0, 128); color:white">
                Back
            </button>
        </p>
        <div class="card py-3 px-3">
            @foreach ($scores as $sessionSemester => $sessionScores)
                <h3>{{ $sessionSemester }}</h3>
                <div class="table-responsive mb-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Assessment</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>History</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessionScores as $score)
                                <tr>
                                    <td>{{ $score->course->title }}</td>
                                    <td>{{ $score->teacher->teacher_title }} {{ $score->teacher->user->fullName() }}</td>
                                    <td>{{ $score->assessment_score }}</td>
                                    <td>{{ $score->exam_score }}</td>
                                    <td>{{ $score->total_score }}</td>
                                    <td>{{ $score->grade }}</td>
                                    <td>{{ ucfirst($score->status) }}</td>
                                    <td>
                                        {{-- <a href="{{ route('admin.score.approval.approved.audit-log', $score) }}"
                                            class="btn btn-primary btn-sm">
                                            View Audit Log
                                        </a> --}}
                                        Log
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
