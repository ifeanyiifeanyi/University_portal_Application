@extends('admin.layouts.admin')

@section('title', 'Lecturer Details')
@section('css')
    <style>
        .lecturer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .info-card {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .course-item,
        .publication-item {
            background-color: white;
            border-radius: 8px;
            height: fit-content;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
        }

        .contact-item {
            margin-bottom: 1rem;
        }

        .contact-icon {
            color: #667eea;
            margin-right: 0.5rem;
        }

        .publication-item {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .publication-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .publication-title {
            font-weight: bold;
        }

        .publication-year {
            font-style: italic;
        }

        .certification-item {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .certification-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .certification-title {
            font-weight: bold;
        }

        .teacher-header {
            padding: 20px;
            border-radius: 10px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            -webkit-text-fill-color: transparent;
        }

        h1.gradient-text {
            font-size: 2.5rem;
            font-weight: bold;
        }

        h1.gradient-text small {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 5px;
        }

        code.gradient-text {
            font-size: 1.8rem;
            padding: 5px 10px;
            border: 2px solid;
            border-image: linear-gradient(135deg, #ff6b6b, #4ecdc4) 1;
        }
    </style>

@endsection



@section('admin')
    <div class="container" style="overflow-x: hidden !important">
        <div class=" mt-5">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center p-3">
                        <center>
                        <img src="{{ empty($teacher->user->profile_photo) ? 'https://via.placeholder.com/150' : asset($teacher->user->profile_photo) }}"
                            alt="Lecturer" class="profile-image mb-3 text-center">
                        </center>
                        <div class="">
                            <h1 class="gradient-text">
                                <small>{{ $teacher->teacher_title ?? ' N/A' }}</small>
                                {{ Str::title($teacher->user->fullName() ?? ' N/A') }}
                            </h1>
                            <code class="lead gradient-text">{{ $teacher->employment_id ?? ' N/A' }}</code>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card text-center">
                        <i class="fas fa-book info-card-icon"></i>
                        <h3>{{ $teacher->teacher_qualification ?? ' N/A' }}</h3>
                        <p>Highest Qualification</p>
                    </div>
                    <div class="col-md-12">
                        {{-- <h2 class="section-title">Contact Information</h2> --}}
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-envelope contact-icon"></i>
                                {{ $teacher->user->email ?? ' N/A' }}
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone contact-icon"></i>
                                {{ $teacher->user->phone ?? ' N/A' }}
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt contact-icon"></i>
                                {{ $teacher->office_address ?? ' N/A' }}
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt contact-icon"></i>
                                {{ $teacher->address ?? ' N/A' }}
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock contact-icon"></i>
                                {{ $teacher->office_hours ?? ' N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>



            <div class="row">
                <div class="col-md-4 h-50">
                    <div class="info-card text-center">
                        <i class="fas fa-user-graduate info-card-icon"></i>
                        <h3>{{ $teacher->number_of_awards ?? ' N/A' }} +</h3>
                        <p>Number of Awards</p>
                    </div>
                </div>
                <div class="col-md-4 h-50">
                    <div class="info-card text-center">
                        <i class="fas fa-user-graduate info-card-icon"></i>
                        <h3>{{ $teacher->teaching_experience ?? ' N/A' }} +</h3>
                        <p>Years of Experience</p>
                    </div>
                </div>

                <div class="col-md-4 h-50">
                    <div class="info-card text-center">
                        <i class="fas fa-award info-card-icon"></i>
                        <h3>{{ $teacher->level ?? ' N/A' }}</h3>
                        <p>Level</p>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <h2 class="section-title">Current Courses</h2>
                    @if ($teacher->teacherAssignments->isNotEmpty())
                        @foreach ($teacher->teacherAssignments as $assignment)
                            <div class="course-item">
                                <h4>{{ $assignment->course->code }}: {{ $assignment->course->title }}</h4>
                                <p>{{ $assignment->academicSession->name }} | {{ $assignment->semester->name }}</p>
                                <p>Department: {{ $assignment->department->name }} </p>
                                <a href="{{ route('admin.teacher.course.show', $assignment->course->id) }}"
                                    class="btn btn-primary btn-sm">Course Details <i
                                        class="fadeIn animated bx bx-chevrons-right"></i></a>
                            </div>
                        @endforeach
                    @else
                        <p>No courses assigned.</p>
                    @endif
                </div>

                <div class="col-md-6">
                    <h2 class="section-title">Current Departments</h2>
                    @php
                        $departments = $teacher->teacherAssignments->pluck('department')->unique('id');
                    @endphp
                    @if ($departments->isNotEmpty())
                        @foreach ($departments as $department)
                            <div class="publication-item">
                                <h5>{{ $department->name }}</h5>
                                <p>Faculty: {{ $department->faculty->name }}</p>
                                <a href="{{ route('admin.teacher.department.show', ['department' => $department->id, 'teacher' => $teacher->id]) }}"
                                    class="btn btn-primary btn-sm">Department Details</a>
                            </div>
                        @endforeach
                    @else
                        <p>No departments assigned.</p>
                    @endif
                </div>
            </div>

            <div class="row mt-5 shadow">
                <div class="col-md-12 ">
                    <h2 class="section-title">About Me</h2>
                    <div class="card">
                        <div class="card-bod">
                            <table class="table table-horizontal table-hover">
                                <tr>
                                    <th>Gender</th>
                                    <th>{{ $teacher->gender }}</th>
                                </tr>
                                <tr>
                                    <th>Date Of birth</th>
                                    <th>{{ $teacher->date_of_birth }}</th>
                                </tr>
                                <tr>
                                    <th>Date of Employment</th>
                                    <th>{{ $teacher->date_of_employment }}</th>
                                </tr>
                                <tr>
                                    <th>Nationality</th>
                                    <th>{{ $teacher->nationality }}</th>
                                </tr>
                                <tr>
                                    <th>Type of Lecturer</th>
                                    <th>{{ $teacher->teacher_type }}</th>
                                </tr>
                                <tr>
                                    <th>Biography</th>
                                    <th>{{ $teacher->biography }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <h2 class="section-title">Certifications</h2>
                    @if (is_array(json_decode($teacher->certifications)) && count(json_decode($teacher->certifications)) > 0)
                        @foreach (json_decode($teacher->certifications) as $index => $certification)
                            @php
                                $hue1 = ($index * 30) % 360;
                                $hue2 = ($hue1 + 30) % 360;
                                $gradient = "linear-gradient(135deg, hsl($hue1, 70%, 80%), hsl($hue2, 70%, 80%))";
                            @endphp
                            <div class="certification-item"
                                style="background: {{ $gradient }}; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h4 class="certification-title" style="margin-top: 0; color: #333;">{{ $certification }}
                                </h4>
                            </div>
                        @endforeach
                    @else
                        <p>No certifications found.</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h2 class="section-title">Publications</h2>
                    @if (is_array(json_decode($teacher->publications)) && count(json_decode($teacher->publications)) > 0)
                        @foreach (json_decode($teacher->publications) as $index => $publication)
                            @php
                                $parts = explode(' - ', $publication);
                                $title = $parts[0] ?? '';
                                $year = $parts[1] ?? '';
                                $hue1 = ($index * 30) % 360;
                                $hue2 = ($hue1 + 30) % 360;
                                $gradient = "linear-gradient(135deg, hsl($hue1, 70%, 80%), hsl($hue2, 70%, 80%))";
                            @endphp
                            <div class="publication-item"
                                style="background: {{ $gradient }}; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h5 class="publication-title" style="margin-top: 0; color: #333;">"{{ $title }}"
                                </h5>
                                <p class="publication-year" style="margin-bottom: 0; color: #666;">Year:
                                    {{ $year }}</p>
                            </div>
                        @endforeach
                    @else
                        <p>No publications found.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt-5">

        </div>
    </div>

@endsection

@section('javascript')

@endsection
