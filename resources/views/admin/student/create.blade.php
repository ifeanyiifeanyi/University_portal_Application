@extends('admin.layouts.admin')

@section('title', 'Create Student Account')

@section('css')
    <style>
        #image-label {
            background: linear-gradient(135deg, #9faef2e5 0%, #b477f2de 100%);
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            left: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            color: white;
            border-radius: 8px;
        }

        #profile_photo {
            display: none;
        }

        .image-preview {
            width: 100%;
            max-width: 150px;
            margin-top: 10px;
            z-index: 70;
        }
    </style>
@endsection

@section('admin')
    <div class="container mt-5">
        @include('admin.alert')
        <div class="card shadow py-3 px-3">
            <h2 class="mb-4 text-center">Create New Student Account</h2>
            <form action="{{ route('admin.student.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body" style="position: relative">
                                <label for="profile_photo" id="image-label">Click to add Photo</label>
                                <input id="profile_photo" type="file" name="profile_photo"
                                    accept=".jpg, .png, image/jpeg, image/png" style="display:none;">
                                <img id="image-preview" src="{{ asset('no_image.jpg') }}" alt="Profile Photo"
                                    class="image-preview">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('first_name') border-danger @enderror"
                            id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                            name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="other_name" class="form-label">Other Name</label>
                        <input type="text" class="form-control @error('other_name') is-invalid @enderror" id="other_name"
                            name="other_name" value="{{ old('other_name') }}">
                        @error('other_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="year_of_admission" class="form-label">Year of Admission</label>
                        <input type="number" class="form-control @error('year_of_admission') is-invalid @enderror"
                            id="year_of_admission" name="year_of_admission" value="{{ old('year_of_admission') }}"
                            min="1900" max="{{ date('Y') }}" required>
                        @error('year_of_admission')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required value="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            required value="password">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" id="department_id"
                            name="department_id" required>
                            <option value="">Select Department</option>
                            @forelse ($departments as $department)
                                <option {{ old('department_id') == $department->id ? 'selected' : '' }}
                                    value="{{ $department->id }}">
                                    {{ $department->code }} : {{ Str::title($department->name) }}
                                </option>
                            @empty
                                <option value=""> ------ -------</option>
                            @endforelse
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="current_level">Academic Level</label>
                            <select class="form-select @error('current_level') is-invalid @enderror" id="current_level"
                                name="current_level" required>
                                <option value="">Select Department First</option>
                            </select>
                            @error('current_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                            id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender"
                            required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="mode_of_entry" class="form-label">Mode of Entry</label>
                        <select class="form-select @error('mode_of_entry') is-invalid @enderror" id="mode_of_entry"
                            name="mode_of_entry" required>
                            <option value="">Select Mode of Entry</option>
                            <option value="UTME" {{ old('mode_of_entry') == 'UTME' ? 'selected' : '' }}>UTME</option>
                            <option value="Direct Entry" {{ old('mode_of_entry') == 'Direct Entry' ? 'selected' : '' }}>
                                Direct Entry</option>
                            <option value="Transfer" {{ old('mode_of_entry') == 'Transfer' ? 'selected' : '' }}>Transfer
                            </option>
                        </select>
                        @error('mode_of_entry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="religion" class="form-label">Religion</label>
                        <input type="text" class="form-control @error('religion') is-invalid @enderror"
                            id="religion" name="religion" value="{{ old('religion') }}" required>
                        @error('religion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state_of_origin" class="form-label">State of Origin(Province)</label>
                        <input type="text" class="form-control @error('state_of_origin') is-invalid @enderror"
                            id="state_of_origin" name="state_of_origin" value="{{ old('state_of_origin') }}" required>
                        @error('state_of_origin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lga_of_origin" class="form-label">LGA of Origin(District)</label>
                        <input type="text" class="form-control @error('lga_of_origin') is-invalid @enderror"
                            id="lga_of_origin" name="lga_of_origin" value="{{ old('lga_of_origin') }}" required>
                        @error('lga_of_origin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hometown" class="form-label">Hometown</label>
                        <input type="text" class="form-control @error('hometown') is-invalid @enderror"
                            id="hometown" name="hometown" value="{{ old('hometown') }}" required>
                        @error('hometown')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nationality" class="form-label">Country</label>
                        <select name="nationality" id="nationality" class="form-control">
                            <option value="" disabled selected>Select Nationality</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->name }}"
                                    {{ old('nationality', 'nigeria') == $country->name ? 'selected' : '' }}>
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('nationality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="residential_address" class="form-label">Residential Address</label>
                        <input type="text" class="form-control @error('residential_address') is-invalid @enderror"
                            id="residential_address" name="residential_address" value="{{ old('residential_address') }}"
                            required>
                        @error('residential_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="permanent_address" class="form-label">Permanent Address</label>
                        <input type="text" class="form-control @error('permanent_address') is-invalid @enderror"
                            id="permanent_address" name="permanent_address" value="{{ old('permanent_address') }}"
                            required>
                        @error('permanent_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="marital_status" class="form-label">Marital Status</label>
                        <select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status"
                            name="marital_status" required>
                            <option value="">Select Marital Status</option>
                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single
                            </option>
                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married
                            </option>
                            <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced
                            </option>
                            <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed
                            </option>
                        </select>
                        @error('marital_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="blood_group" class="form-label">Blood Group</label>
                        <select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group"
                            name="blood_group" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('blood_group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="genotype" class="form-label">Genotype</label>
                        <select class="form-select @error('genotype') is-invalid @enderror" id="genotype"
                            name="genotype" required>
                            <option value="">Select Genotype</option>
                            <option value="AA" {{ old('genotype') == 'AA' ? 'selected' : '' }}>AA</option>
                            <option value="AS" {{ old('genotype') == 'AS' ? 'selected' : '' }}>AS</option>
                            <option value="SS" {{ old('genotype') == 'SS' ? 'selected' : '' }}>SS</option>
                            <option value="AC" {{ old('genotype') == 'AC' ? 'selected' : '' }}>AC</option>
                        </select>
                        @error('genotype')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jamb_registration_number" class="form-label">JAMB Registration Number</label>
                        <input type="text"
                            class="form-control @error('jamb_registration_number') is-invalid @enderror"
                            id="jamb_registration_number" name="jamb_registration_number"
                            value="{{ old('jamb_registration_number') }}">
                        @error('jamb_registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <hr>

                <h4 class="mt-4">Next of Kin Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="next_of_kin_name" class="form-label">Next of Kin Name</label>
                        <input type="text" class="form-control @error('next_of_kin_name') is-invalid @enderror"
                            id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name') }}"
                            required>
                        @error('next_of_kin_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="next_of_kin_relationship" class="form-label">Relationship with Next of Kin</label>
                        <input type="text"
                            class="form-control @error('next_of_kin_relationship') is-invalid @enderror"
                            id="next_of_kin_relationship" name="next_of_kin_relationship"
                            value="{{ old('next_of_kin_relationship') }}" required>
                        @error('next_of_kin_relationship')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="next_of_kin_phone" class="form-label">Next of Kin Phone</label>
                        <input type="tel" class="form-control @error('next_of_kin_phone') is-invalid @enderror"
                            id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}"
                            required>
                        @error('next_of_kin_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="next_of_kin_address" class="form-label">Next of Kin Address</label>
                        <input type="text" class="form-control @error('next_of_kin_address') is-invalid @enderror"
                            id="next_of_kin_address" name="next_of_kin_address" value="{{ old('next_of_kin_address') }}"
                            required>
                        @error('next_of_kin_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ... (rest of the form remains the same) ... -->


                <div class="m-3 pb-5 text-center">

                    <button type="submit" class="btn btn-primary"><i class="fas fa-users-cog"></i> Create Student
                        Account</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profilePhotoInput = document.getElementById('profile_photo');
            const imageLabel = document.getElementById('image-label');
            const imagePreview = document.getElementById('image-preview');

            imageLabel.addEventListener('click', function() {
                profilePhotoInput.click();
            });

            profilePhotoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            const levelSelect = document.getElementById('current_level');

            function updateLevels() {
                const departmentId = departmentSelect.value;
                if (!departmentId) {
                    levelSelect.innerHTML = '<option value="">Select Department First</option>';
                    levelSelect.disabled = true;
                    return;
                }

                levelSelect.innerHTML = '<option value="">Loading levels...</option>';
                levelSelect.disabled = true;

                // First, fetch the department's levels
                fetch(`/admin/departments/${departmentId}/levels`)
                    .then(response => response.json())
                    .then(levels => {
                        levelSelect.innerHTML = '<option value="">Select Level</option>';

                        // For each level, create an option with the correct display text and numeric value
                        levels.forEach(level => {
                            const option = document.createElement('option');

                            // Set the display text to the format-specific level (RN1, ND1, etc.)
                            option.textContent = level;

                            // Use the department's getLevelNumber method to get the correct numeric value
                            // This will convert RN1 to 100, RN2 to 200, etc.
                            if (typeof level === 'string' && (level.startsWith('RN') || level
                                    .startsWith('ND') || level.startsWith('HND') || level.startsWith(
                                        'RMW'))) {
                                switch (level) {
                                    case 'RN1':
                                    case 'ND1':
                                    case 'RMW1':
                                        option.value = '100';
                                        break;
                                    case 'RN2':
                                    case 'ND2':
                                    case 'RMW2':
                                        option.value = '200';
                                        break;
                                    case 'RN3':
                                    case 'RMW3':
                                        option.value = '300';
                                        break;
                                    case 'HND1':
                                        option.value = '300';
                                        break;
                                    case 'HND2':
                                        option.value = '400';
                                        break;
                                    default:
                                        option.value = level;
                                }
                            } else {
                                // For numeric levels, use the level as is
                                option.value = level;
                            }

                            // Set selected option based on old input if it exists
                            if (option.value == "{{ old('current_level') }}") {
                                option.selected = true;
                            }

                            levelSelect.appendChild(option);
                        });
                        levelSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading levels:', error);
                        levelSelect.innerHTML = '<option value="">Error loading levels</option>';
                        levelSelect.disabled = true;
                    });
            }

            departmentSelect.addEventListener('change', updateLevels);
            if (departmentSelect.value) {
                updateLevels();
            }
        });
    </script>
@endsection
