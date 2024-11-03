@extends('student.layouts.student')

@section('title', 'Student Virtual Id Card')
@section('student')
<style>
    <style>
    body {
      background-color: #f8f9fa;
    }
    .id-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      width: 350px;
      padding: 20px;
      margin: 20px auto;
      text-align: center;
      position: relative;
    }
    .id-card .header {
      background-color: #AE152D;
      color: white;
      padding: 10px 0;
      border-radius: 10px 10px 0 0;
    }
    .id-card img {
      border-radius: 50%;
      width: 120px;
      height: 120px;
      object-fit: cover;
      margin: 10px 0;
      border: 5px solid #fff;
    }
    .id-card h5, .id-card p {
      margin: 5px 0;
    }
    .id-card .badge {
      position: absolute;
      top: -10px;
      right: 10px;
      background-color: #ffc107;
      padding: 5px 10px;
      border-radius: 5px;
    }
    .info {
      margin-top: 10px;
    }
    .info .label {
      font-weight: bold;
      color: #6c757d;
    }
  </style>
</style>
@if ($getuser)
<div class="id-card">
    <div class="header">
      <h3>Shanahan University</h3>
      <h6>Student ID Card</h6>
    </div>
    
    <!-- Profile Image -->
    <img src="{{$getuser->profileImage()}}" alt="Student Photo">
    
    <h5 class="mt-3">{{ $getuser->first_name }} {{ $getuser->last_name }} {{ $getuser->other_name }}</h5>
    <p class="text-muted">Student</p>
    
    <div class="info">
      <div class="row mb-2">
        <div class="col-6 text-start">
          <span class="label">Matric No:</span>
        </div>
        <div class="col-6 text-end">
          <span>{{ $student->matric_number }}</span>
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-6 text-start">
          <span class="label">Department:</span>
        </div>
        <div class="col-6 text-end">
          <span>{{ $student->department->name ?? 'Computer Science' }}</span>
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-6 text-start">
          <span class="label">Level:</span>
        </div>
        <div class="col-6 text-end">
          <span>{{ $student->current_level }}</span>
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-6 text-start">
          <span class="label">DOB:</span>
        </div>
        <div class="col-6 text-end">
          <span>{{ $student->date_of_birth }}</span>
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-6 text-start">
          <span class="label">Gender:</span>
        </div>
        <div class="col-6 text-end">
          <span>{{ $student->gender }}</span>
        </div>
      </div>
    </div>
    
    <!-- Footer Section -->
    <div class="badge">Valid Till: {{ now()->addYear()->format('Y') }}</div>
  </div> 
@endif
@endsection