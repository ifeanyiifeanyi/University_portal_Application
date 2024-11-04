@extends('teacher.layouts.teacher')

@section('title', 'Lecturer Dashboard')

@section('teacher')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    {{-- Profile Completion Alert --}}
    @if(empty($teacher->date_of_birth) || empty($teacher->gender) || empty($teacher->teaching_experience) ||
        empty($teacher->teacher_type) || empty($teacher->teacher_qualification) || empty($teacher->teacher_title) ||
        empty($teacher->employment_id) || empty($teacher->date_of_employment) || empty($teacher->address) ||
        empty($teacher->nationality) || empty($teacher->level))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Please complete your profile to access all features.
            <div class="mt-2">
                <a href="{{route('teacher.view.profile')}}" class="btn btn-warning btn-sm">
                    <i class="bi bi-person-fill me-1"></i> Complete Profile
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Courses Stats --}}
        <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Assigned Courses</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light">
                            <i class="bi bi-journal-text text-primary"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{$coursesassignedcount}}</h6>
                            <span class="text-muted small">Current Semester</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Students Stats --}}
        <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                            <i class="bi bi-people text-success"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{$totalStudents ?? 0}}</h6>
                            <span class="text-muted small">Across All Courses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Tasks --}}
        <div class="col-xxl-4 col-md-6">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Pending Tasks</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-light">
                            <i class="bi bi-clock-history text-warning"></i>
                        </div>
                        <div class="ps-3">
                            <h6>{{$pendingTasks ?? 0}}</h6>
                            <span class="text-muted small">Requires Attention</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity Section --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{route('teacher.view.courses')}}" class="btn btn-primary w-100">
                                <i class="bi bi-journal-text me-1"></i> View Courses
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{route('teacher.view.attendance')}}" class="btn btn-success w-100">
                                <i class="bi bi-calendar-check me-1"></i> Take Attendance
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="" class="btn btn-info w-100">
                                <i class="bi bi-pencil-square me-1"></i> Manage Scores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
