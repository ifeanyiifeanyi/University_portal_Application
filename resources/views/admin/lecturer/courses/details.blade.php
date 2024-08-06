@extends('admin.layouts.admin')

@section('title', 'Course Details')

@section('css')
    <style>
        .course-profile-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }

        .course-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .course-header h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .course-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .course-header .credit-hours {
            font-size: 1rem;
            color: #007bff;
            margin-top: 10px;
        }

        .info-section {
            margin-top: 30px;
        }

        .info-section h3 {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        .info-section ul {
            list-style: none;
            padding: 0;
        }

        .info-section ul li {
            background-color: #fff;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .info-section ul li:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .info-section ul li::before {
            content: '🔹';
            display: inline-block;
            margin-right: 10px;
            color: #007bff;
        }
    </style>
@endsection

@section('admin')
    <div class="course-profile-container">
        <div class="course-header">
            <h2>{{ $course->code }}: {{ $course->title }}</h2>
            <p>{{ $course->description }}</p>
            <p class="credit-hours">Credit Hours: {{ $course->credit_hours }}</p>
        </div>

        <div class="info-section">
            <h3><i class="fadeIn animated bx bx-devices"></i> Associated Departments</h3>
            <ul>
                @foreach ($course->departments as $department)
                    <li> {{ $department->name }} - Level: {{ $department->pivot->level }}</li>
                @endforeach
            </ul>
        </div>

        <div class="info-section">
            <h3><i class="fadeIn animated bx bx-book-alt"></i> Assigned Lecturer</h3>
            <ul>
                <li>{{ $course->teachers->first()->user->teacher->teacher_title }}
                    {{ $course->teachers[0]->user->fullName() }}</li>
            </ul>
            {{-- <ul>
                @foreach ($course->teachers as $teacher)
                    <li>{{ $teacher->user->fullName() }}</li>
                @endforeach
            </ul> --}}
        </div>
        <button onclick="history.back()" class="btn btn-primary"> <i class="fadeIn animated bx bx-caret-left-circle"></i> Back to overview</button>
    </div>
@endsection
