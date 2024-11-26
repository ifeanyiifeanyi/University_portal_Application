@extends('admin.layouts.admin')

@section('title', 'Department Credit Load Management')

@section('admin')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Department Credit Load Assignments</h2>
            <a href="{{ route('admin.department.credit.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>New Assignment
            </a>
        </div>




        <div class="row g-4">
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="searchInput" class="form-control"
                                        placeholder="Search by department, semester, level...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select id="departmentFilter" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($creditAssignments->pluck('department_name')->unique() as $department)
                                        <option value="{{ $department }}">{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select id="semesterFilter" class="form-select">
                                    <option value="">All Semesters</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->name }}">{{ $semester->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select id="academicSessionFilter" class="form-select">
                                    <option value="">All Academic Sessions</option>
                                    @foreach ($creditAssignments->pluck('academic_session_name')->unique() as $session)
                                        <option value="{{ $session }}">{{ $session }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($creditAssignments->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle text-info fa-2x mb-3"></i>
                                <p class="text-muted">No credit load assignments have been created yet.</p>
                                <a href="{{ route('admin.department.credit.create') }}" class="btn btn-outline-primary">
                                    Create First Assignment
                                </a>
                            </div>
                        @else
                            @foreach ($creditAssignments->groupBy('department_name') as $department => $assignments)
                                <div class="department-group mb-4" data-department="{{ $department }}">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <small>{{ $loop->iteration }}</small>.
                                        <i class="fas fa-building me-2 text-primary"></i>
                                        {{ $department }}
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Semester</th>
                                                    <th>Academic Session</th>
                                                    <th>Level</th>
                                                    <th>Max Credits</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            {{-- @dd($assignments) --}}
                                            <tbody>
                                                @foreach ($assignments as $assignment)
                                                    <tr class="assignment-row">
                                                        <td>{{ $assignment->semester_name }}</td>
                                                        <td>{{ $assignment->academic_session_name }}</td>
                                                        <td>Level {{ $assignment->level }}</td>
                                                        <td>{{ $assignment->max_credit_hours }} credits</td>
                                                        <td class="text-end">
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('admin.department.credit.edit', $assignment->id) }}"
                                                                    class="btn btn-outline-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form
                                                                    onsubmit="return confirm('Are you sure you want to delete this assignment?')"
                                                                    action="{{ route('admin.department.credit.delete', $assignment->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm border-info">
                    <div class="card-header bg-info bg-opacity-10 border-info">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>About Credit Load Assignment
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            This section manages the maximum credit hours students can register per semester. Each
                            assignment is specific to a department, academic level, and semester combination.
                        </p>
                        <div class="alert alert-light border mt-3">
                            <strong>Key Features:</strong>
                            <ul class="list-unstyled mb-0 mt-2">
                                <li><i class="fas fa-filter me-2 text-muted"></i>Filter by department</li>
                                <li><i class="fas fa-search me-2 text-muted"></i>Search assignments</li>
                                <li><i class="fas fa-layer-group me-2 text-muted"></i>Grouped view</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Credit load assignments are required for course registration.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const departmentFilter = document.getElementById('departmentFilter');
            const departmentGroups = document.querySelectorAll('.department-group');

            function filterContent() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDepartment = departmentFilter.value.toLowerCase();

                departmentGroups.forEach(group => {
                    const departmentName = group.dataset.department.toLowerCase();
                    const rows = group.querySelectorAll('.assignment-row');
                    let groupVisible = false;

                    // Check department filter first
                    if (!selectedDepartment || departmentName === selectedDepartment) {
                        // Then apply search filter
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            const visible = text.includes(searchTerm);
                            row.style.display = visible ? '' : 'none';
                            if (visible) groupVisible = true;
                        });
                    }

                    group.style.display = groupVisible ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterContent);
            departmentFilter.addEventListener('change', filterContent);
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const departmentFilter = document.getElementById('departmentFilter');
            const semesterFilter = document.getElementById('semesterFilter');
            const academicSessionFilter = document.getElementById('academicSessionFilter');
            const departmentGroups = document.querySelectorAll('.department-group');

            function filterContent() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDepartment = departmentFilter.value.toLowerCase();
                const selectedSemester = semesterFilter.value.toLowerCase();
                const selectedAcademicSession = academicSessionFilter.value.toLowerCase();

                departmentGroups.forEach(group => {
                    const departmentName = group.dataset.department.toLowerCase();
                    const rows = group.querySelectorAll('.assignment-row');
                    let groupVisible = false;

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        const semesterText = row.querySelector('td:nth-child(1)').textContent
                            .toLowerCase(); // Adjust based on column position
                        const academicSessionText = row.querySelector('td:nth-child(2)').textContent
                            .toLowerCase(); // Adjust based on column position

                        const matchesSearch = text.includes(searchTerm);
                        const matchesDepartment = !selectedDepartment || departmentName ===
                            selectedDepartment;
                        const matchesSemester = !selectedSemester || semesterText ===
                            selectedSemester;
                        const matchesAcademicSession = !selectedAcademicSession ||
                            academicSessionText === selectedAcademicSession;

                        const visible = matchesSearch && matchesDepartment && matchesSemester &&
                            matchesAcademicSession;
                        row.style.display = visible ? '' : 'none';
                        if (visible) groupVisible = true;
                    });

                    group.style.display = groupVisible ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterContent);
            departmentFilter.addEventListener('change', filterContent);
            semesterFilter.addEventListener('change', filterContent);
            academicSessionFilter.addEventListener('change', filterContent);
        });
    </script>
@endsection
