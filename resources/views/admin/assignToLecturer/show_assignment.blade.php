@extends('admin.layouts.admin')

@section('title', 'Assignment Details')
@section('css')

@endsection

@section('admin')
    <div class="container">
        @include('admin.return_btn')

        <div class="row">
            <!-- Left side: Assignment Details -->
            <div class="col-md-7">
                <div class="card px-2 py-3 mx-auto my-3">
                    <h3 class="text-center">{{ $assignment->department->name }}</h3>
                    <p><strong>Lecturer:</strong>
                        {{ $assignment->teacher->teacher_title . ' ' . $assignment->teacher->user->fullName() }}</p>
                    <p><strong>Course:</strong> {{ $assignment->course->code }} - {{ $assignment->course->title }}</p>
                    <p><strong>Academic Session:</strong> {{ $assignment->academicSession->name }}</p>
                    <p><strong>Semester:</strong> {{ $assignment->semester->name }}</p>
                    <p><strong>Level:</strong>
                        @foreach ($assignment->course->courseAssignments as $courseAssignment)
                            @if (
                                $courseAssignment->department_id == $assignment->department_id &&
                                    $courseAssignment->semester_id == $assignment->semester_id)
                                {{ $courseAssignment->level }}
                            @endif
                        @endforeach
                    </p>
                    <p><strong>Assigned On:</strong>
                        {{ \Carbon\Carbon::parse($assignment->created_at)->format('jS F Y g:i A') }}</p>
                    <p><strong>Updated On:</strong>
                        {{ \Carbon\Carbon::parse($assignment->updated_at)->format('jS F Y g:i A') }}</p>
                    <p>
                        <a href="{{ route('admin.teacher.assignment.view') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('admin.teacher.assignment.edit', $assignment->id) }}" class="btn btn-primary">Edit
                            Assignment</a>

                    </p>
                    <p>

                    </p>
                </div>
            </div>

            <!-- Right side: Lecturer Details -->
            <div class="col-md-5">
                <div class="card px-2 py-3 mx-auto my-3">
                    <h3 class="text-center">Lecturer Details</h3>
                    <p class="text-center mx-2 my-2">
                        <img src="{{ empty($assignment->teacher->user->profile_photo) ? asset('no_image.jpg') : asset($assignment->teacher->user->profile_photo) }}"
                            alt="" class="thumbnail img-responsive w-50">
                    </p>
                    <p><strong>Name:</strong>
                        {{ $assignment->teacher->teacher_title . ' ' . $assignment->teacher->user->fullName() }}</p>
                    <p><strong>Date of Birth:</strong>
                        {{ \Carbon\Carbon::parse($assignment->teacher->date_of_birth)->format('jS F Y') }}</p>
                    <p><strong>Gender:</strong> {{ $assignment->teacher->gender }}</p>
                    <p><strong>Teaching Experience:</strong> {{ $assignment->teacher->teaching_experience }}</p>
                    <p><strong>Type:</strong> {{ $assignment->teacher->teacher_type }}</p>
                    <p><strong>Qualification:</strong> {{ $assignment->teacher->teacher_qualification }}</p>
                    <p><strong>Office Hours:</strong> {{ $assignment->teacher->office_hours }}</p>
                    <p><strong>Office Address:</strong> {{ $assignment->teacher->office_address }}</p>
                    <p><strong>Biography:</strong> {{ $assignment->teacher->biography }}</p>
                    <p><strong>Certifications:</strong>
                        @if ($assignment->teacher->certifications)
                            <ul>
                                @foreach (json_decode($assignment->teacher->certifications) as $certification)
                                    <li>{{ $certification }}</li>
                                @endforeach
                            </ul>
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Publications:</strong>
                        @if ($assignment->teacher->publications)
                            <ul>
                                @foreach (json_decode($assignment->teacher->publications) as $publication)
                                    <li>{{ $publication }}</li>
                                @endforeach
                            </ul>
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Awards:</strong> {{ $assignment->teacher->number_of_awards }}</p>
                    <p><strong>Employment ID:</strong> {{ $assignment->teacher->employment_id }}</p>
                    <p><strong>Date of Employment:</strong>
                        {{ \Carbon\Carbon::parse($assignment->teacher->date_of_employment)->format('jS F Y') }}</p>
                    <p><strong>Address:</strong> {{ $assignment->teacher->address }}</p>
                    <p><strong>Nationality:</strong> {{ $assignment->teacher->nationality }}</p>
                    <p><strong>Level:</strong> {{ $assignment->teacher->level }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

@endsection
