@extends('admin.layouts.admin')

@section('title', 'Rejected Scores')

@section('admin')
    <div class="container">
        <!-- Existing code -->

        <form id="rejectedScoresForm" action="{{ route('admin.score.approval.rejected.bulk-accept') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped">
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
                                <td>{{ $score->course->title }}</td>
                                <td>{{ $score->department->name }}</td>
                                <td>{{ $score->teacher->teacher_title }} {{ $score->teacher->user->fullName() }}</td>
                                <td>{{ $score->assessment_score }}</td>
                                <td>{{ $score->exam_score }}</td>
                                <td>{{ $score->total_score }}</td>
                                <td>{{ $score->grade }}</td>
                                <td>{{ ucfirst($score->status) }}</td>
                                <td>
                                    <a href="{{ route('admin.score.approval.rejected.revert', $score) }}"
                                        class="btn btn-primary btn-sm">
                                        Revert Rejection
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
                <button type="submit" class="btn btn-success">Accept Selected</button>
                <button type="submit" class="btn btn-danger"
                    formaction="{{ route('admin.score.approval.rejected.bulk-revert') }}">Revert Selected</button>
            </div>
        </form>

        {{ $rejectedScores->links() }}
    </div>
@endsection
