@extends('admin.layouts.admin')

@section('title', 'Department Details')

@section('css')
    <style>
        .course-card {
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .teacher-link {
            color: #007bff;
            text-decoration: none;
        }

        .teacher-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('admin')
    <div class="container mt-4">
        <h3 class="mb-4 text-center">{{ Str::title($department->name) }} Department</h3>
        <h5 class="mb-4 text-center">{{ $department->code }}</h5>
        <p>{!! e($department->description)!!}</p>
<hr>
        <form action="{{ route('admin.department.show', $department->id) }}" method="GET">
            <div class="row mb-4">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Search courses or teachers"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="session" class="form-control">
                        <option value="">All Sessions</option>
                        @foreach ($assignments->pluck('semester.academicSession.name')->unique() as $session)
                            <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                                {{ $session }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="semester" class="form-control">
                        <option value="">All Semesters</option>
                        @foreach ($assignments->pluck('semester.name')->unique() as $semester)
                            <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                                {{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="level" class="form-control">
                        <option value="">All Levels</option>
                        @foreach ($assignments->pluck('level')->unique() as $level)
                            <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                {{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="btn-group mb-4" role="group">
            <button type="button" class="btn btn-secondary" id="grid-view">Grid</button>
            <button type="button" class="btn btn-secondary" id="list-view">List</button>
        </div>

        <div class="row" id="assignments-container">
            @forelse ($assignments as $assignment)
                <div class="col-md-6 col-lg-4 mb-4 course-card">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $assignment->course->title }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $assignment->course->code }}</h6>
                            <p class="card-text">
                                <strong>Level:</strong> {{ $assignment->level }}<br>
                                <strong>Session:</strong> {{ $assignment->semester->academicSession->name }}<br>
                                <strong>Semester:</strong> {{ $assignment->semester->name }}
                            </p>
                            <p>
                                <strong>Faculty: </strong> {{ $department->faculty->name }}
                            </p>
                            <p>
                                <strong>Assigned Teacher(s):</strong>
                                @if ($assignment->teacherAssignments->isNotEmpty())
                                    @foreach ($assignment->teacherAssignments as $teacherAssignment)
                                        @if ($teacherAssignment->teacher)
                                            <a href="{{ route('admin.teacher.show', $teacherAssignment->teacher->id) }}"
                                                class="teacher-link">
                                                {{ $teacherAssignment->teacher->user->fullName() }}
                                            </a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    Not assigned yet
                                @endif
                            </p>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Last updated: {{ $assignment->updated_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center">No course assignments found.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $assignments->links() }}
        </div>
    </div>

    <button id="back-to-top" class="btn btn-primary btn-sm mb-3"
        style="position: fixed; bottom: 20px; right: 20px; display: none;">Back to Top</button>
@endsection

@section('javascript')
    <script>
        document.getElementById('grid-view').addEventListener('click', function() {
            const container = document.getElementById('assignments-container');
            container.classList.remove('flex-column');
            document.querySelectorAll('.course-card').forEach(function(card) {
                card.classList.remove('col-12');
                card.classList.add('col-md-6', 'col-lg-4');
            });
        });

        document.getElementById('list-view').addEventListener('click', function() {
            const container = document.getElementById('assignments-container');
            container.classList.add('flex-column');
            document.querySelectorAll('.course-card').forEach(function(card) {
                card.classList.remove('col-md-6', 'col-lg-4');
                card.classList.add('col-12');
            });
        });

        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("back-to-top").style.display = "block";
            } else {
                document.getElementById("back-to-top").style.display = "none";
            }
        };

        document.getElementById('back-to-top').addEventListener('click', function() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        });
    </script>
@endsection
