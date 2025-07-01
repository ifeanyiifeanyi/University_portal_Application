@extends('student.layouts.student')

@section('title', 'Edit Profile')
@section('student')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.4.1/css/all.min.css" />

    <style>
        :root {
            --primary-color: #0d382e;
            --primary-light: rgba(13, 56, 46, 0.1);
            --primary-dark: #082821;
            --success-color: #198754;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .edit-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .edit-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }

        .form-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .form-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow-lg);
        }

        .form-card-header {
            background: var(--primary-light);
            border-bottom: 2px solid var(--primary-color);
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .form-card-body {
            padding: 1.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 56, 46, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            color: white;
        }

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-1px);
            color: white;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .readonly-field {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .text-danger {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .required-field::after {
            content: '*';
            color: #dc3545;
            margin-left: 4px;
        }

        .form-section {
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
            margin-bottom: 2rem;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .edit-header {
                text-align: center;
                padding: 1.5rem;
            }

            .form-card-body {
                padding: 1rem;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <div class="container-fluid mt-3">
        <!-- Breadcrumb -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--primary-color);">Edit Profile</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('student.view.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('student.view.profile') }}">Profile</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        @if ($student)
            <!-- Edit Header -->
            <div class="edit-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-2">
                            <i class="fas fa-edit me-2"></i>Edit Profile Information
                        </h2>
                        <p class="mb-0">Update your personal and academic information</p>
                    </div>
                    <div class="col-md-4 text-center text-md-end">
                        <a href="{{ route('student.view.profile') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            @include('messages')

            <form action="{{ route('student.update.profile') }}" method="POST" id="profileUpdateForm">
                @csrf

                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-12">
                        <div class="form-card">
                            <div class="form-card-header">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </div>
                            <div class="form-card-body">
                                <div class="row">


                                    <!-- JAMB Registration Number (Read-only) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">JAMB Registration Number</label>
                                        <input type="text" class="form-control readonly-field"
                                            name="jamb_registration_number" value="{{ $student->jamb_registration_number }}"
                                            readonly>
                                        @error('jamb_registration_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- First Name (Read-only) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control readonly-field" name="firstname"
                                            value="{{ $student->user->first_name }}" readonly>
                                        @error('firstname')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Last Name (Read-only) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control readonly-field" name="lastname"
                                            value="{{ $student->user->last_name }}" readonly>
                                        @error('lastname')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Other Names -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Other Names</label>
                                        <input type="text" class="form-control" name="othernames"
                                            value="{{ old('othernames', $student->user->other_name) }}">
                                        @error('othernames')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" name="date_of_birth"
                                            value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}">
                                        @error('date_of_birth')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Gender -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-control @error('gender') invalid-feedback @enderror ">
                                            <option value="" disabled {{ !$student->gender ? 'selected' : '' }}>
                                                Select gender</option>
                                            <option value="Male"
                                                {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option value="Other"
                                                {{ old('gender', $student->gender) == 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phonenumber"
                                            value="{{ old('phonenumber', $student->user?->phone) }}">
                                        @error('phonenumber')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email (Read-only) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control readonly-field"
                                            value="{{ $student->user?->email }}" readonly>
                                    </div>

                                    <!-- Marital Status -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Marital Status</label>
                                        <select name="marital_status" class="form-control">
                                            <option value="">Select status</option>
                                            <option value="Single"
                                                {{ old('marital_status', $student->marital_status) == 'Single' ? 'selected' : '' }}>
                                                Single</option>
                                            <option value="Married"
                                                {{ old('marital_status', $student->marital_status) == 'Married' ? 'selected' : '' }}>
                                                Married</option>
                                            <option value="Divorced"
                                                {{ old('marital_status', $student->marital_status) == 'Divorced' ? 'selected' : '' }}>
                                                Divorced</option>
                                            <option value="Widowed"
                                                {{ old('marital_status', $student->marital_status) == 'Widowed' ? 'selected' : '' }}>
                                                Widowed</option>
                                        </select>
                                        @error('marital_status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Religion -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Religion</label>
                                        <input type="text" class="form-control" name="religion"
                                            value="{{ old('religion', $student->religion) }}">
                                        @error('religion')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="col-12">
                        <div class="form-card">
                            <div class="form-card-header">
                                <i class="fas fa-map-marker-alt me-2"></i>Address Information
                            </div>
                            <div class="form-card-body">
                                <div class="row">
                                    <!-- Nationality -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nationality</label>
                                        <select name="nationality" class="form-control">
                                            <option value="">Select nationality</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->name }}" {{ old('nationality', $student->nationality) == $country->name ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('nationality')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- State of Origin -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">State of Origin</label>
                                        <select name="state_of_origin" class="form-control">
                                            <option value="">Select state</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->name }}" {{ old('state_of_origin', $student->state_of_origin) == $state->name ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('state_of_origin')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- LGA/Province -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Local Government/Province</label>
                                        <input type="text" class="form-control" name="local_govt_of_origin"
                                            value="{{ old('local_govt_of_origin', $student->lga_of_origin) }}">
                                        @error('local_govt_of_origin')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Hometown -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hometown</label>
                                        <input type="text" class="form-control" name="hometown"
                                            value="{{ old('hometown', $student->hometown) }}">
                                        @error('hometown')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Residential Address -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Residential Address</label>
                                        <textarea class="form-control" name="residential_address" rows="3">{{ old('residential_address', $student->residential_address) }}</textarea>
                                        @error('residential_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Permanent Address -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Permanent Address</label>
                                        <textarea class="form-control" name="permanent_address" rows="3">{{ old('permanent_address', $student->permanent_address) }}</textarea>
                                        @error('permanent_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="col-12">
                        <div class="form-card">
                            <div class="form-card-header">
                                <i class="fas fa-heartbeat me-2"></i>Medical Information
                            </div>
                            <div class="form-card-body">
                                <div class="row">
                                    <!-- Blood Group -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Blood Group</label>
                                        <select name="bloodgroup" class="form-control">
                                            <option value="">Select blood group</option>
                                            <option value="A+"
                                                {{ old('bloodgroup', $student->blood_group) == 'A+' ? 'selected' : '' }}>A+
                                            </option>
                                            <option value="A-"
                                                {{ old('bloodgroup', $student->blood_group) == 'A-' ? 'selected' : '' }}>A-
                                            </option>
                                            <option value="B+"
                                                {{ old('bloodgroup', $student->blood_group) == 'B+' ? 'selected' : '' }}>B+
                                            </option>
                                            <option value="B-"
                                                {{ old('bloodgroup', $student->blood_group) == 'B-' ? 'selected' : '' }}>B-
                                            </option>
                                            <option value="AB+"
                                                {{ old('bloodgroup', $student->blood_group) == 'AB+' ? 'selected' : '' }}>
                                                AB+</option>
                                            <option value="AB-"
                                                {{ old('bloodgroup', $student->blood_group) == 'AB-' ? 'selected' : '' }}>
                                                AB-</option>
                                            <option value="O+"
                                                {{ old('bloodgroup', $student->blood_group) == 'O+' ? 'selected' : '' }}>O+
                                            </option>
                                            <option value="O-"
                                                {{ old('bloodgroup', $student->blood_group) == 'O-' ? 'selected' : '' }}>O-
                                            </option>
                                            <option value="AC+"
                                                {{ old('bloodgroup', $student->blood_group) == 'AC+' ? 'selected' : '' }}>
                                                AC+</option>
                                            <option value="AC-"
                                                {{ old('bloodgroup', $student->blood_group) == 'AC-' ? 'selected' : '' }}>
                                                AC-</option>
                                            <option value="Rh+"
                                                {{ old('bloodgroup', $student->blood_group) == 'Rh+' ? 'selected' : '' }}>
                                                Rh+</option>
                                            <option value="Rh-"
                                                {{ old('bloodgroup', $student->blood_group) == 'Rh-' ? 'selected' : '' }}>
                                                Rh-</option>
                                        </select>
                                        @error('bloodgroup')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!-- Genotype -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Genotype</label>
                                        <select name="genotype" class="form-control">
                                            <option value="">Select genotype</option>
                                            <option value="AA"
                                                {{ old('genotype', $student->genotype) == 'AA' ? 'selected' : '' }}>AA
                                            </option>
                                            <option value="AS"
                                                {{ old('genotype', $student->genotype) == 'AS' ? 'selected' : '' }}>AS
                                            </option>
                                            <option value="AC"
                                                {{ old('genotype', $student->genotype) == 'AC' ? 'selected' : '' }}>AC
                                            </option>
                                            <option value="SS"
                                                {{ old('genotype', $student->genotype) == 'SS' ? 'selected' : '' }}>SS
                                            </option>
                                            <option value="SC"
                                                {{ old('genotype', $student->genotype) == 'SC' ? 'selected' : '' }}>SC
                                            </option>
                                            <option value="CC"
                                                {{ old('genotype', $student->genotype) == 'CC' ? 'selected' : '' }}>CC
                                            </option>
                                        </select>
                                        @error('genotype')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next of Kin Information -->
                <div class="col-12">
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fas fa-users me-2"></i>Next of Kin Information
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <!-- Next of Kin Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Next of Kin Name</label>
                                    <input type="text" class="form-control" name="next_of_kin_name"
                                        value="{{ old('next_of_kin_name', $student->next_of_kin_name) }}">
                                    @error('next_of_kin_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Next of Kin Relationship -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Relationship</label>
                                    <input type="text" class="form-control" name="next_of_kin_relationship"
                                        value="{{ old('next_of_kin_relationship', $student->next_of_kin_relationship) }}">
                                    @error('next_of_kin_relationship')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Next of Kin Phone -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="next_of_kin_phone"
                                        value="{{ old('next_of_kin_phone', $student->next_of_kin_phone) }}">
                                    @error('next_of_kin_phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Next of Kin Address -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="next_of_kin_address" rows="3">{{ old('next_of_kin_address', $student->next_of_kin_address) }}</textarea>
                                    @error('next_of_kin_address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="col-12">
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fas fa-graduation-cap me-2"></i>Academic Information
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <!-- Year of Admission -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Year of Admission</label>
                                    <input type="number" class="form-control" name="year_of_admission" min="1900"
                                        max="{{ date('Y') }}"
                                        value="{{ old('year_of_admission', $student->year_of_admission) }}">
                                    @error('year_of_admission')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Mode of Entry -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mode of Entry</label>
                                    <select name="mode_of_entry" class="form-control">
                                        <option value="">Select mode of entry</option>
                                        <option value="JAMB"
                                            {{ old('mode_of_entry', $student->mode_of_entry) == 'JAMB' ? 'selected' : '' }}>
                                            JAMB</option>
                                        <option value="Direct Entry"
                                            {{ old('mode_of_entry', $student->mode_of_entry) == 'Direct Entry' ? 'selected' : '' }}>
                                            Direct Entry</option>
                                        <option value="Transfer"
                                            {{ old('mode_of_entry', $student->mode_of_entry) == 'Transfer' ? 'selected' : '' }}>
                                            Transfer</option>
                                        <option value="Change of Course"
                                            {{ old('mode_of_entry', $student->mode_of_entry) == 'Change of Course' ? 'selected' : '' }}>
                                            Change of Course</option>
                                    </select>
                                    @error('mode_of_entry')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>
                </div>




                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-card">
                            <div class="form-card-body text-center">
                                <button type="submit" class="btn btn-primary-custom me-3">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <a href="{{ route('student.view.profile') }}" class="btn btn-secondary-custom">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Student profile not found. Please contact the administrator.
            </div>
        @endif
    </div>

    <script>
        // Form validation and preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileUpdateForm');
            const fileInput = document.querySelector('input[name="profile_photo"]');
            const previewImg = document.querySelector('.profile-photo-preview img');

            // File input change handler for image preview
            if (fileInput && previewImg) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                            this.value = '';
                            return;
                        }

                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('File size must be less than 2MB');
                            this.value = '';
                            return;
                        }

                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Form submission handler
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Add loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
                    }
                });
            }

            // Auto-hide success/error messages
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>

@endsection
