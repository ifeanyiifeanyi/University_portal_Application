@extends('admin.layouts.admin')

@section('title', 'Assignment Details')
@section('css')
<style>
    .details-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        height: 100%;
    }

    .card-title {
        color: #2d3436;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f2f6;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-label {
        color: #636e72;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #2d3436;
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .profile-image {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 2rem;
        border: 4px solid #f1f2f6;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-back {
        background-color: #576574;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background-color: #222f3e;
        color: white;
    }

    .btn-edit {
        background-color: #10ac84;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background-color: #0a8967;
        color: white;
    }

    .list-unstyled {
        list-style: none;
        padding-left: 0;
    }

    .list-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f2f6;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .date-info {
        font-size: 0.9rem;
        color: #636e72;
        font-style: italic;
    }
</style>
@endsection

@section('admin')
    <div class="container py-4">



        <div class="row g-4">
            <!-- Left side: Assignment Details -->
            <div class="col-md-7">
                <div class="details-card">
                    <h3 class="card-title">Assignment Information</h3>

                    <div class="info-group">
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $assignment->department->name }}</div>

                        <div class="info-label">Lecturer</div>
                        <div class="info-value">
                            {{ $assignment->teacher->teacher_title . ' ' . $assignment->teacher->user->fullName() }}</div>

                        <div class="info-label">Course Details</div>
                        <div class="info-value">{{ $assignment->course->code }} - {{ $assignment->course->title }}</div>

                        <div class="info-label">Academic Information</div>
                        <div class="info-value">
                            <div>Session: {{ $assignment->academicSession->name }}</div>
                            <div>Semester: {{ $assignment->semester->name }}</div>
                            <div>Level:
                                {{-- @dump($assignment) --}}
                                {{-- @foreach ($assignment->course->courseAssignments as $courseAssignment)
                                    @if (
                                        $courseAssignment->department_id == $assignment->department_id &&
                                            $courseAssignment->semester_id == $assignment->semester_id)
                                        {{ $courseAssignment->level }}
                                    @endif
                                @endforeach --}}
                                {{ $assignment->course->courseAssignments->first()->level}}

                            </div>
                        </div>

                        <div class="info-label">Timeline</div>
                        <div class="info-value date-info">
                            <div>Assigned: {{ \Carbon\Carbon::parse($assignment->created_at)->format('jS F Y g:i A') }}
                            </div>
                            <div>Last Updated: {{ \Carbon\Carbon::parse($assignment->updated_at)->format('jS F Y g:i A') }}
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('admin.teacher.assignment.view') }}" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <a href="{{ route('admin.teacher.assignment.edit', $assignment->id) }}" class="btn btn-edit">
                            <i class="fas fa-edit me-2"></i>Edit Assignment
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right side: Lecturer Details -->
            <div class="col-md-5">
                <div class="details-card">
                    <h3 class="card-title">Lecturer Profile</h3>

                    <div class="text-center">
                        <img src="{{ empty($assignment->teacher->user->profile_photo) ? asset('no_image.jpg') : asset($assignment->teacher->user->profile_photo) }}"
                            alt="Lecturer Profile" class="profile-image">
                    </div>

                    <div class="info-group">
                        <div class="info-label">Personal Information</div>
                        <div class="info-value">
                            <div>{{ $assignment->teacher->teacher_title . ' ' . $assignment->teacher->user->fullName() }}
                            </div>
                            <div>Born: {{ \Carbon\Carbon::parse($assignment->teacher->date_of_birth)->format('jS F Y') }}
                            </div>
                            <div>Gender: {{ $assignment->teacher->gender }}</div>
                            <div>Nationality: {{ $assignment->teacher->nationality }}</div>
                        </div>

                        <div class="info-label">Professional Details</div>
                        <div class="info-value">
                            <div>Experience: {{ $assignment->teacher->teaching_experience }}</div>
                            <div>Type: {{ $assignment->teacher->teacher_type }}</div>
                            <div>Qualification: {{ $assignment->teacher->teacher_qualification }}</div>
                            <div>Level: {{ $assignment->teacher->level }}</div>
                        </div>

                        <div class="info-label">Contact Information</div>
                        <div class="info-value">
                            <div>Office Hours: {{ $assignment->teacher->office_hours }}</div>
                            <div>Office: {{ $assignment->teacher->office_address }}</div>
                            <div>Address: {{ $assignment->teacher->address }}</div>
                        </div>

                        @if ($assignment->teacher->certifications)
                            <div class="info-label">Certifications</div>
                            <div class="info-value">
                                <ul class="list-unstyled">
                                    @foreach (json_decode($assignment->teacher->certifications) as $certification)
                                        <li class="list-item">{{ $certification }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($assignment->teacher->publications)
                            <div class="info-label">Publications</div>
                            <div class="info-value">
                                <ul class="list-unstyled">
                                    @foreach (json_decode($assignment->teacher->publications) as $publication)
                                        <li class="list-item">{{ $publication }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="info-label">Awards & Recognition</div>
                        <div class="info-value">Number of Awards: {{ $assignment->teacher->number_of_awards }}</div>

                        <div class="info-label">Employment Details</div>
                        <div class="info-value">
                            <div>ID: {{ $assignment->teacher->employment_id }}</div>
                            <div>Started:
                                {{ \Carbon\Carbon::parse($assignment->teacher->date_of_employment)->format('jS F Y') }}
                            </div>
                        </div>

                        @if ($assignment->teacher->biography)
                            <div class="info-label">Biography</div>
                            <div class="info-value">{{ $assignment->teacher->biography }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

@endsection
