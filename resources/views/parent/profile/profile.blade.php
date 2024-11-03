@extends('parent.layouts.parent')

@section('title', 'Parent Dashboard')
@section('parent')
<div class="container mt-4">
    <div class="d-flex justify-content-center">
        <div class="col-xl-6">
            <div class="card parent-profile-card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Parent Profile</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $parent->user->profileImage()}}" alt="Parent Picture" class="rounded-circle profile-picture">
                        <h4 class="mt-3">{{ $parent->user->first_name }} {{ $parent->user->last_name }} {{ $parent->user->other_name }}</h4>
                        <p class="text-muted">{{ $parent->occupation }}</p>
                    </div>
                    
                    <div class="profile-info">
                        <h5>Personal Information</h5>
                        <p><strong>Username:</strong> {{ $parent->user->username }}</p>
                        <p><strong>Email:</strong> {{ $parent->user->email }}</p>
                        <p><strong>Phone:</strong> {{ $parent->user->phone }}</p>
                        <p><strong>Gender:</strong> {{ $parent->gender }}</p>
                        <p><strong>Marital Status:</strong> {{ $parent->marital_status }}</p>
                        <p><strong>Religion:</strong> {{ $parent->religion }}</p>
                        <p><strong>Occupation:</strong> {{ $parent->occupation }}</p>

                        <h5>Address Information</h5>
                        <p><strong>Nationality:</strong> {{ $parent->nationality }}</p>
                        <p><strong>State of Origin:</strong> {{ $parent->state_of_origin }}</p>
                        <p><strong>LGA of Origin:</strong> {{ $parent->lga_of_origin }}</p>
                        <p><strong>Hometown:</strong> {{ $parent->hometown }}</p>
                        <p><strong>Residential Address:</strong> {{ $parent->residential_address }}</p>
                        <p><strong>Permanent Address:</strong> {{ $parent->permanent_address }}</p>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection