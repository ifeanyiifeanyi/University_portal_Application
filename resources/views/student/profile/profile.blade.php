@extends('student.layouts.student')

@section('title', 'Student profile')
@section('student')
  <style>
.student-profile-card {
    border: none;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.student-profile-card .card-header {
    border-radius: 15px 15px 0 0;
}

.profile-picture {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 5px solid #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.profile-info {
    padding: 20px;
}

.profile-info h5 {
    color: #0d382e;
    border-bottom: 2px solid #0d382e;
    padding-bottom: 10px;
    margin-top: 30px;
    margin-bottom: 20px;
}

.profile-info p {
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 5px;
}

.profile-info strong {
    font-weight: 600;
    color: #333;
}

@media (max-width: 768px) {
    .profile-info p {
        flex-direction: column;
    }
}
</style>
<div class="container-xxl mt-3">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Profile</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>
    </div>
    @if ($student)


    <div class="row">

          <div class="col-xl-6">
            {{-- <div class="card student-profile-card">
                <div class="card-header text-white" style="background: #157020; color : #ffffff">
                    <h3 class="mb-0">Student Profile</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{$getuser->profileImage()}}" alt="Student Picture" class="rounded-circle profile-picture">
                        <h4 class="mt-3">{{ $getuser->first_name }} {{ $getuser->last_name }} {{ $getuser->other_name }}</h4>
                        <p class="text-muted">Matric Number: {{ $student->matric_number }}</p>
                    </div>

                    <div class="profile-info">
                        <h5>Personal Information</h5>
                        <p><strong>JAMB Reg. Number:</strong> {{ $student->jamb_registration_number }}</p>
                        <p><strong>Date of Birth:</strong> {{ $student->date_of_birth }}</p>
                        <p><strong>Gender:</strong> {{ $student->gender }}</p>
                        <p><strong>Phone:</strong> {{ $getuser->phone }}</p>
                        <p><strong>Email:</strong> {{ $getuser->email }}</p>
                        <p><strong>Marital Status:</strong> {{ $student->marital_status }}</p>
                        <p><strong>Religion:</strong> {{ $student->religion }}</p>
                        <p><strong>Blood Group:</strong> {{ $student->blood_group }}</p>
                        <p><strong>Genotype:</strong> {{ $student->genotype }}</p>

                        <h5>Address Information</h5>
                        <p><strong>Nationality:</strong> {{ $student->nationality }}</p>
                        <p><strong>State of Origin:</strong> {{ $student->state_of_origin }}</p>
                        <p><strong>LGA/Province:</strong> {{ $student->lga_of_origin }}</p>
                        <p><strong>Hometown:</strong> {{ $student->hometown }}</p>
                        <p><strong>Residential Address:</strong> {{ $student->residential_address }}</p>
                        <p><strong>Permanent Address:</strong> {{ $student->permanent_address }}</p>

                        <h5>Next of Kin Information</h5>
                        <p><strong>Name:</strong> {{ $student->next_of_kin_name }}</p>
                        <p><strong>Relationship:</strong> {{ $student->next_of_kin_relationship }}</p>
                        <p><strong>Phone:</strong> {{ $student->next_of_kin_phone }}</p>
                        <p><strong>Address:</strong> {{ $student->next_of_kin_address }}</p>

                        <h5>Academic Information</h5>
                        <p><strong>Department:</strong> {{ $student->department->name }}</p>
                        <p><strong>Year of Admission:</strong> {{ $student->year_of_admission }}</p>
                        <p><strong>Mode of Entry:</strong> {{ $student->mode_of_entry }}</p>
                        <p><strong>Current Level:</strong> {{ $student->current_level }}</p>

                    </div>
                </div>
            </div> --}}

            <div class="card student-profile-card">
                <div class="card-header text-white" style="background: #0d382e; color : #ffffff">
                    <h3 class="mb-0">Student Profile</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $student->user->profileImage() }}" alt="Student Picture" class="rounded-circle profile-picture">
                        <h4 class="mt-3">{{ $student->user->first_name }} {{ $student->user->last_name }} {{ $student->user->other_name }}</h4>
                        <p class="text-muted">Matric Number: {{ $student->matric_number }}</p>
                    </div>

                    <div class="profile-info">
                        <h5>Personal Information</h5>
                        <p><strong>JAMB Reg. Number:</strong> {{ $student->jamb_registration_number }}</p>
                        <p><strong>Date of Birth:</strong> {{ $student->date_of_birth }}</p>
                        <p><strong>Gender:</strong> {{ $student->gender }}</p>
                        <p><strong>Phone:</strong> {{ $student->user->phone }}</p>
                        <p><strong>Email:</strong> {{ $student->user->email }}</p>
                        <p><strong>Marital Status:</strong> {{ $student->marital_status }}</p>
                        <p><strong>Religion:</strong> {{ $student->religion }}</p>
                        <p><strong>Blood Group:</strong> {{ $student->blood_group }}</p>
                        <p><strong>Genotype:</strong> {{ $student->genotype }}</p>

                        <h5>Address Information</h5>
                        <p><strong>Nationality:</strong> {{ $student->nationality }}</p>
                        <p><strong>State of Origin:</strong> {{ $student->state_of_origin }}</p>
                        <p><strong>LGA/Province:</strong> {{ $student->lga_of_origin }}</p>
                        <p><strong>Hometown:</strong> {{ $student->hometown }}</p>
                        <p><strong>Residential Address:</strong> {{ $student->residential_address }}</p>
                        <p><strong>Permanent Address:</strong> {{ $student->permanent_address }}</p>

                        <h5>Next of Kin Information</h5>
                        <p><strong>Name:</strong> {{ $student->next_of_kin_name }}</p>
                        <p><strong>Relationship:</strong> {{ $student->next_of_kin_relationship }}</p>
                        <p><strong>Phone:</strong> {{ $student->next_of_kin_phone }}</p>
                        <p><strong>Address:</strong> {{ $student->next_of_kin_address }}</p>

                        <h5>Academic Information</h5>
                        <p><strong>Department:</strong> {{ $student->department->name }}</p>
                        <p><strong>Year of Admission:</strong> {{ $student->year_of_admission }}</p>
                        <p><strong>Mode of Entry:</strong> {{ $student->mode_of_entry }}</p>
                        <p><strong>Current Level:</strong> {{ $student->current_level }}</p>
                    </div>
                </div>
            </div>
        </div>
          {{-- <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="{{$getuser->profileImage()}}" alt="Profile" class="rounded-circle">
              <h2>{{$getuser->fullName()}}</h2>
            </div>
          </div> --}}

        <div class="col-xl-6">


          <div class="card">
            <div class="card-header">
              <h2>Personal details</h2>
            </div>
            <div class="card-body pt-3">
              @include('messages')
                @if (!$student)
                @include('student.profile.createprofile')
                    @else
                @include('student.profile.updateprofile')
                @endif

            </div>
          </div>

        </div>
      </div>
      @endif
</div>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const departmentSelect = document.getElementById('department_id');
        const levelSelect = document.getElementById('level');
        const  department = document.getElementById('department').value;

        function updateLevels() {
            const departmentId = departmentSelect.value;
            fetch(`/student/fees/departments/${department}/levels`)
                .then(response => response.json())
                .then(levels => {
                  console.log(levels);
                    levelSelect.innerHTML = '';
                    levels.forEach(level => {
                        const option = document.createElement('option');
                        option.value = level;
                        option.textContent = level;
                        levelSelect.appendChild(option);
                    });
                });
        }

        departmentSelect.addEventListener('change', updateLevels);
        updateLevels(); // Initial population
    });
  </script> --}}
@endsection
