@extends('admin.layouts.admin')

@section('title', 'Create Lecturer Account')

@section('css')
    <style>
        .edit-teacher-container {
            background-color: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }

        .section-title {
            color: #3a3a3a;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

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
    <div class="container edit-teacher-container">
        <h2 class="section-title">Create Lecturer Account</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.teacher.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                            name="first_name" value="{{ old('first_name') }}">
                        @error('first_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="last_name">Surname</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                            name="last_name" value="{{ old('last_name') }}">
                        @error('last_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="other_name">Other Names</label>
                        <input type="text" class="form-control @error('other_name') is-invalid @enderror" id="other_name"
                            name="other_name" value="{{ old('other_name') }}">
                        @error('other_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                            id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Male' ? 'Female' : '' }}> Female</option>

                        </select>
                        @error('gender')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="teaching_experience">Teaching Experience (years i number)</label>
                        <input type="text" class="form-control @error('teaching_experience') is-invalid @enderror"
                            id="teaching_experience" name="teaching_experience" value="{{ old('teaching_experience') }}">
                        @error('teaching_experience')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="teacher_type">Teacher Type</label>
                        <select class="form-select @error('teacher_type') is-invalid @enderror" id="teacher_type"
                            name="teacher_type">
                            <option value="">Select Teacher Type</option>
                            <option value="Full-time" {{ old('teacher_type') == 'Full-time' ? 'selected' : '' }}> Full-time
                            </option>
                            <option value="Part-time" {{ old('teacher_type') == 'Part-time' ? 'selected' : '' }}> Part-time
                            </option>
                            <option value="Auxiliary" {{ old('teacher_type') == 'Auxiliary' ? 'selected' : '' }}> Auxiliary
                            </option>
                        </select>
                        @error('teacher_type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="teacher_qualification">Teacher Qualification</label>
                        <input type="text" class="form-control @error('teacher_qualification') is-invalid @enderror"
                            id="teacher_qualification" name="teacher_qualification"
                            value="{{ old('teacher_qualification') }}">
                        @error('teacher_qualification')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="teacher_title">Teacher Title</label>
                        <select class="form-control @error('teacher_title') is-invalid @enderror" id="teacher_title" name="teacher_title">
                            <option value="" disabled selected>Select Title</option>
                            <option value="Prof." {{ old('teacher_title') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                            <option value="Dr." {{ old('teacher_title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                            <option value="Mr." {{ old('teacher_title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                            <option value="Mrs." {{ old('teacher_title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                            <option value="Ms." {{ old('teacher_title') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                            <option value="Miss" {{ old('teacher_title') == 'Miss' ? 'selected' : '' }}>Miss</option>
                            <option value="Sr." {{ old('teacher_title') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="Fr." {{ old('teacher_title') == 'Fr.' ? 'selected' : '' }}>Fr.</option>
                            <option value="Br." {{ old('teacher_title') == 'Br.' ? 'selected' : '' }}>Br.</option>
                            <option value="RN" {{ old('teacher_title') == 'RN' ? 'selected' : '' }}>RN</option>
                            <option value="NP" {{ old('teacher_title') == 'NP' ? 'selected' : '' }}>NP</option>
                            <option value="CNM" {{ old('teacher_title') == 'CNM' ? 'selected' : '' }}>CNM</option>
                            <option value="DNP" {{ old('teacher_title') == 'DNP' ? 'selected' : '' }}>DNP</option>
                            <option value="PhD" {{ old('teacher_title') == 'PhD' ? 'selected' : '' }}>PhD</option>
                            <option value="EdD" {{ old('teacher_title') == 'EdD' ? 'selected' : '' }}>EdD</option>
                            <option value="MSc" {{ old('teacher_title') == 'MSc' ? 'selected' : '' }}>MSc</option>
                            <option value="MA" {{ old('teacher_title') == 'MA' ? 'selected' : '' }}>MA</option>
                            <option value="MPH" {{ old('teacher_title') == 'MPH' ? 'selected' : '' }}>MPH</option>
                            <option value="MBA" {{ old('teacher_title') == 'MBA' ? 'selected' : '' }}>MBA</option>
                            <option value="Engr." {{ old('teacher_title') == 'Engr.' ? 'selected' : '' }}>Engr.</option>
                        </select>
                        @error('teacher_title')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="office_hours">Office Hours</label>
                        <input type="text" class="form-control @error('office_hours') is-invalid @enderror"
                            id="office_hours" name="office_hours" value="{{ old('office_hours') }}">
                        @error('office_hours')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">

                        <div class="col-md-6">
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
                    <!--end row-->
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="office_address">Office Address</label>
                        <input type="text" class="form-control @error('office_address') is-invalid @enderror"
                            id="office_address" name="office_address" value="{{ old('office_address') }}">
                        @error('office_address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="editor">Biography</label>
                        <textarea class="form-control @error('biography') is-invalid @enderror" id="editor" name="biography"
                            rows="3">{{ old('biography') }}</textarea>
                        @error('biography')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="certifications">Certifications</label>
                        <div id="certifications-container">

                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="certifications[]" value="">
                                <button type="button" class="btn btn-danger remove-certification"><i
                                        class="fas fa-times"></i></button>
                            </div>

                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" id="add-certification"><i
                                class="fas fa-plus"></i></button>
                        @error('certifications')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>

                    <div class="form-group">
                        <label for="publications">Publications</label>
                        <div id="publications-container">

                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="publications[]" value="">
                                <button type="button" class="btn btn-danger remove-publication"><i
                                        class="fas fa-times"></i></button>
                            </div>

                        </div>
                        <button type="button" class="btn btn-primary btn-sm  mt-2" id="add-publication"><i
                            class="fas fa-plus"></i></button>
                        @error('publications')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="number_of_awards">Number of Awards</label>
                        <input type="number" class="form-control @error('number_of_awards') is-invalid @enderror"
                            id="number_of_awards" name="number_of_awards" value="{{ old('number_of_awards') }}">
                        @error('number_of_awards')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="date_of_employment">Date of Employment</label>
                        <input type="date" class="form-control @error('date_of_employment') is-invalid @enderror"
                            id="date_of_employment" name="date_of_employment" value="{{ old('date_of_employment') }}">
                        @error('date_of_employment')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" value="{{ old('address') }}">
                        @error('address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nationality">Country</label>
                        <select name="nationality" class="form-control">
                            <option value="" selected disabled>Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->name }}" {{ old("nationality") == $country->name ? 'selected' : '' }}> {{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('nationality')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="level">Level</label>
                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level">
                            <option value="">Select Level</option>
                            <option value="Senior Lecturer" {{ old('level') == 'Senior Lecturer' ? 'selected' : '' }}>
                                Senior Lecturer </option>
                            <option value="Junior Lecturer" {{ old('level') == 'Junior Lecturer' ? 'selected' : '' }}>
                                Junior Lecturer</option>
                            <option value="Technician" {{ old('level') == 'Technician' ? 'selected' : '' }}>Technician
                            </option>
                        </select>
                        @error('level')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-save">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const certificationsContainer = document.getElementById('certifications-container');
            const addCertificationButton = document.getElementById('add-certification');

            function addCertificationInput(value = '') {
                const inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group', 'mb-2');

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'certifications[]';
                input.classList.add('form-control');
                input.value = value;

                const button = document.createElement('button');
                button.type = 'button';
                button.classList.add('btn', 'btn-danger', 'remove-certification');
                button.innerHTML = '<i class="fas fa-times"></i>'; // Use FontAwesome icon

                inputGroup.appendChild(input);
                inputGroup.appendChild(button);
                certificationsContainer.appendChild(inputGroup);

                // Add event listener to the remove button
                button.addEventListener('click', function() {
                    inputGroup.remove();
                });
            }
            // Add event listener to the add button
            addCertificationButton.addEventListener('click', function() {
                addCertificationInput();
            });

            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-certification').forEach(function(button) {
                button.addEventListener('click', function() {
                    button.closest('.input-group').remove();
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const publicationsContainer = document.getElementById('publications-container');
            const addPublicationButton = document.getElementById('add-publication');

            // Function to add a new publication input field
            function addPublicationInput(value = '') {
                const inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group', 'mb-2');

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'publications[]';
                input.classList.add('form-control');
                input.value = value;

                const button = document.createElement('button');
                button.type = 'button';
                button.classList.add('btn', 'btn-danger', 'remove-publication');
                button.innerHTML = "<i class='fas fa-times'></i>";

                inputGroup.appendChild(input);
                inputGroup.appendChild(button);
                publicationsContainer.appendChild(inputGroup);

                // Add event listener to the remove button
                button.addEventListener('click', function() {
                    inputGroup.remove();
                });
            }

            // Add event listener to the add button
            addPublicationButton.addEventListener('click', function() {
                addPublicationInput();
            });

            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-publication').forEach(function(button) {
                button.addEventListener('click', function() {
                    button.closest('.input-group').remove();
                });
            });
        });
    </script>
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
@endsection
