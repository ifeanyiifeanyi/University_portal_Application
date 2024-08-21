@extends('admin.layouts.admin')


@section('title', 'Registered Students')

@section('admin')
    <div class="container">
        @include('admin.alert')
        <div class="card py-3 px-3">
            <span>
                <button class="btn btn-primary mb-2" onclick="history.back()">Back</button>
            </span>
            <p><b>Department: </b> {{ $assignment->department->name }}</p>
            <p>Registered Students for: <b>{{ $assignment->course->title }}</b></p>
            <p>
                <b>Semester:</b> {{ $assignment->semester->name }} | <b>Academic Session:
                    {{ $assignment->academicSession->name }}</b>
            </p>

            <div class="d-flex  align-items-center mb-3">
                <div class="me-3">
                    <a href="{{ route('admin.export.scores', $assignment->id) }}" class="btn btn-primary">Export Template</a>
                </div>
                <form action="{{ route('admin.import.scores', $assignment->id) }}" method="POST"
                    enctype="multipart/form-data" class="d-flex align-items-center">
                    @csrf
                    <input type="file" name="csv_file" class="form-control me-2" required>
                    @error('csv_file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn btn-dark">Import</button>
                </form>
            </div>
        </div>

        <div class="card py-3 px-3">
            <form action="{{ route('admin.store.scores', $assignment->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    {{-- @dd($enrollments) --}}
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
                                    @php
                                        $previousScore = $studentScores->get($enrollment->student_id);
                                    @endphp

                                    <tr>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $enrollment->student->matric_number }}</td>
                                        <td>{{ $enrollment->student->user->fullName() }}</td>
                                        <td>
                                            <input type="number" name="scores[{{ $enrollment->id }}][assessment]"
                                                class="form-control assessment-score" min="0" max="40"
                                                step="0.01"
                                                value="{{ old('scores.' . $enrollment->id . '.assessment', $previousScore->assessment_score ?? 0) }}"
                                                required>
                                            @error('scores.' . $enrollment->id . '.assessment')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror

                                        </td>

                                        <td>
                                            <input type="number" name="scores[{{ $enrollment->id }}][exam]"
                                                class="form-control exam-score" min="0" max="60" step="0.01"
                                                value="{{ old('scores.' . $enrollment->id . '.exam', $previousScore->exam_score ?? 0) }}"
                                                required>
                                            @error('scores.' . $enrollment->id . '.exam')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror

                                        </td>

                                        <td>
                                            <input type="number" name="scores[{{ $enrollment->id }}][total]"
                                                class="form-control total-score"
                                                value="{{ $previousScore->total_score ?? '' }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="scores[{{ $enrollment->id }}][grade]"
                                                class="form-control grade" value="{{ $previousScore->grade ?? '' }}"
                                                readonly>
                                        </td>
                                        <td class="status">
                                            {{ $previousScore ? ($previousScore->is_failed ? 'Failed' : 'Passed') : '' }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Save Scores</button>
                </div>
            </form>
        </div>
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
