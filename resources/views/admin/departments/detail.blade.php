@extends('admin.layouts.admin')

@section('title', 'Department Code: ' . $department->code)

@section('css')
    <style>
        .course-card {
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-10px);
            /* box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); */
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
    <div class="container">
        {{-- @dd($department->departmentHead) --}}

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Department Head</h5>
                        <p class="card-text">
                            @if ($department->departmentHead)
                                {{ $department->departmentHead->teacher->title_and_full_name }}
                            @else
                                Not assigned yet
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Program</h5>
                        <p class="card-text">
                            @if ($department->program)
                                {{ $department->program->name }}
                            @else
                                Not assigned yet
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Duration</h5>
                        <p class="card-text">{{ $department->duration }} years</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Faculty</h5>
                        <p class="card-text">{{ $department->faculty->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Lecturers</h5>
                        <p class="card-text">{{ $department->teachers->count() }} Lecturers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text">{{ $department->students->count() }} Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Courses</h5>
                        <p class="card-text">{{ $department->courses->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Contact</h5>
                        <samp class="card-text">{{ $department->phone }}</samp>
                        <samp class="card-text">{{ $department->email }}</samp>

                    </div>
                </div>
            </div>
        </div>

    </div>



    <div class="card shadow px-3 py-3">

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
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="btn-group mb-4" role="group">
        <button type="button" class="btn btn-secondary" id="grid-view"><i class="fas fa-th-large"></i> Grid</button>
        <button type="button" class="btn btn-secondary" id="list-view"><i class="fas fa-box"></i> List</button>
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
