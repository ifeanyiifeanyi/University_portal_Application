@extends('admin.layouts.admin')


@section('title', 'Registered Students')

@section('admin')
    <div class="container">
        {{-- @dd($enrollments) --}}
        <p><b>Department: </b> {{ $assignment->department->name }}</p>
        <p>Registered Students for:  <b>{{ $assignment->course->title }}</b></p>
        <p><b>Semester:</b> {{ $assignment->semester->name }} | <b>Academic Session: {{ $assignment->academicSession->name }}</b></p>

        <form action="{{ route('admin.store.scores', $assignment->id) }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>sn</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Assessment Score (40%)</th>
                            <th>Exam Score (60%)</th>
                            <th>Total Score</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enrollments as $enrollment)
                            @if ($enrollment->semesterCourseRegistration->status === 'approved')
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $enrollment->student->matric_number }}</td>
                                    <td>{{ $enrollment->student->user->fullName() }}</td>
                                    <td>
                                        <input type="number" name="scores[{{ $enrollment->id }}][assessment]"
                                            class="form-control assessment-score" min="0" max="40" step="0.01"
                                            value="{{ old('scores.' . $enrollment->id . '.assessment', $enrollment->studentScore->assessment_score ?? 0) }}"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" name="scores[{{ $enrollment->id }}][exam]"
                                            class="form-control exam-score" min="0" max="60" step="0.01"
                                            value="{{ old('scores.' . $enrollment->id . '.exam', $enrollment->studentScore->exam_score ?? 0) }}"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" name="scores[{{ $enrollment->id }}][total]"
                                            class="form-control total-score" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="scores[{{ $enrollment->id }}][grade]"
                                            class="form-control grade" readonly>
                                    </td>
                                    <td class="status"></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">Save Scores</button>
            </div>
        </form>

    </div>
@endsection
@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const assessmentInput = row.querySelector('.assessment-score');
                const examInput = row.querySelector('.exam-score');
                const totalInput = row.querySelector('.total-score');
                const gradeInput = row.querySelector('.grade');
                const statusCell = row.querySelector('.status');

                function validateInput(input, max) {
                    input.addEventListener('input', function(e) {
                        let value = parseFloat(e.target.value);
                        if (isNaN(value) || value < 0) {
                            e.target.value = '';
                        } else if (value > max) {
                            e.target.value = '';
                        }
                    });
                }

                validateInput(assessmentInput, 40);
                validateInput(examInput, 60);

                function calculateTotal() {
                    const assessment = parseFloat(assessmentInput.value) || 0;
                    const exam = parseFloat(examInput.value) || 0;
                    const total = assessment + exam;

                    totalInput.value = total.toFixed(2);

                    fetch(`/admin/get-grade/${total}`)
                        .then(response => response.json())
                        .then(data => {
                            gradeInput.value = data.grade;
                            statusCell.textContent = data.status;
                            statusCell.classList.toggle('text-danger', data.status === 'Failed');
                        });
                }

                assessmentInput.addEventListener('change', calculateTotal);
                examInput.addEventListener('change', calculateTotal);
            });
        });
    </script>

@endsection
