@extends('admin.layouts.admin')

@section('title', 'Department Details')

@section('css')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .department-header {
            position: relative;
            background-color: #4a90e2;
            color: white;
            padding: 40px 20px 60px;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
            margin-bottom: 60px;
        }

        .department-header h1 {
            font-size: 3rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .department-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .department-info p {
            margin: 5px 0;
            font-size: 1.1rem;
        }

        .courses-container {
            position: relative;
        }

        .courses-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background-color: #e9ecef;
            z-index: -1;
        }

        .course-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .course-card::before {
            content: "";
            position: absolute;
            top: 50%;
            left: -15px;
            width: 30px;
            height: 30px;
            background-color: #4a90e2;
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .card-header {
            background-color: #4a90e2;
            color: white;
            font-weight: bold;
            border-bottom: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        .course-level {
            display: inline-block;
            background-color: #ffc107;
            color: #212529;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .btn-submit-scores {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-submit-scores:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .department-header h1 {
                font-size: 2rem;
            }
        }
    </style>
@endsection

@section('admin')
    <div class="container">
        <div class="department-header">
            <h1>{{ $department->name }}</h1>
            <div class="department-info">
                <p>Faculty: {{ $department->faculty->name }}</p>
                <p>Duration: {{ $department->duration }} years</p>
            </div>
        </div>

        <h2 class="text-center mb-5">Taught by {{ $teacher->user->teacher->teacher_title }} {{ $teacher->user->fullName() }}
        </h2>

        <div class="courses-container">
            {{-- @dd($teacherAssignments) --}}
            @if ($teacherAssignments->isNotEmpty())
                @foreach ($teacherAssignments as $assignment)
                    <div class="card course-card">
                        <div class="card-header">
                            {{ $assignment->course->code }}: {{ $assignment->course->title }}
                        </div>
                        <div class="card-body">
                            <p class="card-text">Session: {{ $assignment->academicSession->name }}</p>
                            <p class="card-text">Semester: {{ $assignment->semester->name }}</p>
                            <p class="card-text">
                                Level:
                                <span class="course-level">
                                    @if ($assignment->courseAssignment)
                                        {{ $assignment->courseAssignment->level }}
                                    @elseif($assignment->course->courseAssignments->isNotEmpty())
                                        {{ $assignment->course->courseAssignments->where('department_id', $department->id)->first()->level ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </p>
                            <a href="{{ route('teacher.course.students', ['teacherId' => $assignment->teacher_id,'courseId' => $assignment->course_id, 'semesterId' => $assignment->semester_id, 'academicSessionId' => $assignment->academic_session_id]) }}"
                                class="btn btn-primary btn-submit-scores mt-3">View Students & Submit Scores</a>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center">No courses assigned to this teacher in this department.</p>
            @endif
        </div>
    </div>
@endsection
