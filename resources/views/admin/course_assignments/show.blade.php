@extends('admin.layouts.admin')

@section('title', '' . $semester->name)

@section('css')
    <style>
        .department-card {
            transition: all 0.3s ease;
        }

        .department-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .level-card {
            transition: all 0.3s ease;
        }

        .level-card:hover {
            transform: translateY(-5px);
        }

        .course-row:hover {
            background-color: #f8f9fa;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #fff;
        }

        .form-select,
        .form-control,
        .btn {
            padding: 0.5rem 1rem;
            height: 42px;
        }

        .input-group-text {
            padding: 0.5rem 1rem;
        }
    </style>
@endsection

@section('admin')
    <div class="container-fluid">
        <div class="sticky-header bg-light border-bottom shadow-sm p-4">
            <div class="text-center mb-4">
                <h3 class="mb-2">Course Assignments for</h3>
                <h4 class="mb-2 fw-bold text-secondary">{{ $semester->name }}</h4>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge bg-dark border">{{ $semester->academicSession->name }}</span>
                    @if ($semester->academicSession->is_current)
                        <span class="text-secondary">
                            <i class="fas fa-calendar-check me-1"></i>
                            Current Academic Session
                        </span>
                    @endif
                </div>
                <hr class="my-3">
            </div>

            <form action="{{ route('course-assignments.show', $semester->id) }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-secondary"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search courses..."
                                name="search" value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $filterDepartment == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="level" class="form-select">
                            <option value="">All Levels</option>
                            @foreach ($levels as $lvl)
                                <option value="{{ $lvl }}" {{ $filterLevel == $lvl ? 'selected' : '' }}>
                                    Level {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-light border w-100 d-flex align-items-center justify-content-center gap-2"
                            type="submit">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            @forelse ($departments as $department)
                @if (isset($groupedAssignments[$department->id]))
                    @php
                        $maxCreditHours =
                            $department
                                ->semesters()
                                ->where('semester_id', $semester->id)
                                ->first()->pivot->max_credit_hours ?? 'N/A';
                    @endphp
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm department-card">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="lead mb-0">{{ $department->name }}</h4>
                                    <span class="badge bg-light text-dark border">
                                        Max Credit Hours: <strong>{{ $maxCreditHours }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                @forelse ($groupedAssignments[$department->id] as $level => $levelAssignments)
                                    <div class="card level-card mb-3">
                                        <div class="card-header bg-light border-bottom">
                                            <h5 class="lead mb-0 d-flex align-items-center">
                                                <i class="fas fa-layer-group me-2 text-secondary"></i>
                                                Level {{ $level }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Title</th>
                                                            <th>Credits</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($levelAssignments as $assignment)
                                                            <tr class="course-row">
                                                                <td>{{ $assignment->course->code }}</td>
                                                                <td>{{ $assignment->course->title }}</td>
                                                                <td>{{ $assignment->course->credit_hours }}</td>
                                                                <td>
                                                                    <button
                                                                        class="btn btn-light btn-sm border-0 delete-assignment"
                                                                        data-id="{{ $assignment->id }}"
                                                                        data-course="{{ $assignment->course->title }}"
                                                                        title="Delete Assignment">
                                                                        <x-delete-icon />
                                                                    </button>
                                                                    <form id="delete-form-{{ $assignment->id }}"
                                                                        action="{{ route('course-assignments.destroy', $assignment) }}"
                                                                        method="POST" class="d-none">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-light border text-center">
                                        No courses assigned for this department.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="alert alert-light border text-center">
                        No course assignments found for this semester.
                    </div>
                </div>
            @endforelse
        </div>
        <a href="{{ route('course-assignments.index') }}" class="btn btn-secondary mt-3">Back to Overview</a>
    </div>

    <div class="row">
        @forelse ($departments as $department)
            @if(isset($groupedAssignments[$department->id]))
                <div class="col-12 mb-4">
                    <div class="card department-card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="lead text-light">{{ $department->name }}</h4>
                        </div>
                        <div class="card-body">
                            @forelse ($groupedAssignments[$department->id] as $level => $levelAssignments)
                                <div class="card level-card mb-3">
                                    <div class="card-header card border-top border-0 border-4 border-secondary">
                                        <h5 class="lead text-muted">Level {{ $level }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Title</th>
                                                        <th>Credits</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($levelAssignments as $assignment)
                                                        <tr class="course-row">
                                                            <th>{{ $assignment->course->code }}</th>
                                                            <th>{{ $assignment->course->title }}</th>
                                                            <th>{{ $assignment->course->credit_hours }}</th>
                                                            <th>
                                                                <form action="{{ route('course-assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button style="background: transparent" type="submit" class="border-0" onclick="return confirm('Are you sure?')">
                                                                        <x-delete-icon />
                                                                    </button>
                                                                </form>
                                                            </th>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p>No courses assigned for this department.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-12">
                <p>No course assignments found for this semester.</p>
            </div>
        @endforelse
    </div>

    <a href="{{ route('course-assignments.index') }}" class="btn btn-secondary mt-3">Back to Overview</a>
</div>
@endsection

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.querySelector('form');
        const inputs = searchForm.querySelectorAll('input, select');

        inputs.forEach(input => {
            input.addEventListener('change', () => searchForm.submit());
        });
    });
</script>
@endsection

@section('javascript')
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     const searchForm = document.querySelector('form');
        //     const inputs = searchForm.querySelectorAll('input, select');

        //     inputs.forEach(input => {
        //         input.addEventListener('change', () => searchForm.submit());
        //     });
        // });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter form submission
            const searchForm = document.querySelector('form');
            const inputs = searchForm.querySelectorAll('input, select');

            inputs.forEach(input => {
                input.addEventListener('change', () => searchForm.submit());
            });

            // Delete confirmation with SweetAlert2
            const deleteButtons = document.querySelectorAll('.delete-assignment');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const assignmentId = this.getAttribute('data-id');
                    const courseName = this.getAttribute('data-course');

                    Swal.fire({
                        title: 'Are you sure?',
                        html: `You are about to delete the course assignment for:<br><strong>${courseName}</strong>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${assignmentId}`).submit();
                        }
                    });
                });
            });

            // Show success message if exists
            @if (session('message'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('message') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            // Show success message if exists
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif


        });
    </script>
@endsection
