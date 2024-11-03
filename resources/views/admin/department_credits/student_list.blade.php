@extends('admin.layouts.admin')

@section('title', 'Student Course Registration Management')

@section('admin')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Student Course Registration</h2>
        <div class="text-muted">
            <span class="badge bg-primary">{{ $currentSemester->name }}</span>
            <span class="badge bg-secondary">{{ $currentAcademicSession->name }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text"
                                       id="searchInput"
                                       class="form-control"
                                       placeholder="Search by name, matric no...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="departmentFilter" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($students->pluck('department.name', 'department.id')->unique() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="registrationStatus" class="form-select">
                                <option value="">All Registration Status</option>
                                <option value="pending">Pending</option>
                                <option value="registered">Registered</option>
                                <option value="not_registered">Not Registered</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>no</th>
                                    <th>Student Name</th>
                                    <th>Matric No</th>
                                    <th>Department</th>
                                    <th>Level</th>
                                    <th>Registration Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr class="student-row"
                                        data-department="{{ $student->department_id }}"
                                        data-status="{{ $student->semesterRegistrations->first()?->status ?? 'not_registered' }}">
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $student->user->full_name }}</td>
                                        <td>{{ $student->matric_number }}</td>
                                        <td>{{ $student->department->name }}</td>
                                        <td>Level {{ $student->current_level }}</td>
                                        <td>
                                            @php
                                                $registration = $student->semesterRegistrations->first();
                                                $status = $registration?->status ?? 'not_registered';
                                                $statusClass = [
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    'not_registered' => 'bg-secondary'
                                                ][$status];
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ str_replace('_', ' ', ucfirst($status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.assign.courseForStudent', $student) }}"
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-book-open me-1"></i>
                                                    Register Courses
                                                </a>
                                                @if($registration)
                                                    <a href="{{ route('admin.students.course-registrations', $student) }}"
                                                       class="btn btn-outline-info">
                                                        <i class="fas fa-eye me-1"></i>
                                                        View Registration
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-users text-muted fa-2x mb-3"></i>
                                            <p class="text-muted mb-0">No students found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('registrationStatus');
    const studentRows = document.querySelectorAll('.student-row');

    function filterStudents() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedDepartment = departmentFilter.value;
        const selectedStatus = statusFilter.value;

        studentRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const rowDepartment = row.dataset.department;
            const rowStatus = row.dataset.status;

            const matchesSearch = rowText.includes(searchTerm);
            const matchesDepartment = !selectedDepartment || rowDepartment === selectedDepartment;
            const matchesStatus = !selectedStatus || rowStatus === selectedStatus;

            row.style.display = (matchesSearch && matchesDepartment && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterStudents);
    departmentFilter.addEventListener('change', filterStudents);
    statusFilter.addEventListener('change', filterStudents);
});
</script>
@endsection
