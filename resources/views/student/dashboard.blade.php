@extends('student.layouts.student')

@section('title', 'Student Dashboard')
<style>
    :root {
        --primary-color: #0d382e;
        --secondary-color: #20c997;
    }
    body {
        background-color: #f4f7f6;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .dashboard-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 15px;
    }
</style>
@section('student')

<div class="container-fluid">
    @php
    // Create a collection from the teacher's attributes and check if any is empty
    $incompleteProfile = collect($student->getAttributes())->except(['deleted_at','cgpa'])->contains(function ($value) {
        return empty($value);
    });
@endphp

@if($incompleteProfile)
<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
    <strong>Profile Incomplete!</strong> Please complete your profile to access all features.
    <a href="{{route('student.view.profile')}}" class="btn btn-primary btn-sm ms-3">Complete Profile</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-4">
    <div class="col-12">
        <h2 class="mb-4 text-center">Welcome, {{ $student->user->fullName() }}!</h2>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-money-bill-wave dashboard-icon"></i>
                <h5 class="card-title">Total Fees Paid</h5>
                <p class="card-text fs-4 fw-bold">₦{{ number_format($totalfees, 2) }}</p>
                <a href="{{ route('student.view.payments') }}" class="btn btn-primary w-100">
                    View All Payments
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-graduation-cap dashboard-icon"></i>
                <h5 class="card-title">Academic Performance</h5>
                <p class="card-text fs-4 fw-bold">CGPA: {{ $student->cgpa }}</p>
                <a href="{{ route('student.view.result.select') }}" class="btn btn-primary w-100">
                    View Results
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-id-card dashboard-icon"></i>
                <h5 class="card-title">Virtual ID Card</h5>
                @if($incompleteProfile)
                    <p class="text-warning">Complete profile to view</p>
                @else
                    <p class="card-text">Your digital student ID</p>
                    <a href="{{route('student.view.virtualid')}}" class="btn btn-primary w-100">
                        View ID Card
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-edit dashboard-icon"></i>
                <h5 class="card-title">Profile Management</h5>
                <p class="card-text">Update your personal details</p>
                <a href="{{route('student.view.profile')}}" class="btn btn-primary w-100">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">Quick Actions</h4>
        <div class="d-flex gap-3">
            <a href="{{ route('student.view.courseregistration') }}" class="btn btn-outline-primary">
                <i class="fas fa-book me-2"></i>My Courses
            </a>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt me-2"></i>Class Schedule
            </a>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-list me-2"></i>Exam Schedule
            </a>
        </div>
    </div>
</div>

                    </div> <!-- container-fluid -->
@endsection
