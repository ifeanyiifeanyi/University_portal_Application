@extends('admin.layouts.admin')

@section('title', 'Edit Teacher Details')

@section('css')
<style>
    .edit-teacher-container {
        background-color: #f8f9fa;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
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
    .form-control, .form-select {
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
</style>
@endsection

@section('admin')
<div class="container edit-teacher-container">
    <h2 class="section-title">Edit Teacher Details</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.teachers.update', $teacher->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $teacher->date_of_birth) }}">
                    @error('date_of_birth')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $teacher->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $teacher->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $teacher->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="teaching_experience">Teaching Experience (years)</label>
                    <input type="text" class="form-control @error('teaching_experience') is-invalid @enderror" id="teaching_experience" name="teaching_experience" value="{{ old('teaching_experience', $teacher->teaching_experience) }}">
                    @error('teaching_experience')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="teacher_type">Teacher Type</label>
                    <select class="form-select @error('teacher_type') is-invalid @enderror" id="teacher_type" name="teacher_type">
                        <option value="">Select Teacher Type</option>
                        <option value="Full-time" {{ old('teacher_type', $teacher->teacher_type) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ old('teacher_type', $teacher->teacher_type) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Auxiliary" {{ old('teacher_type', $teacher->teacher_type) == 'Auxiliary' ? 'selected' : '' }}>Auxiliary</option>
                    </select>
                    @error('teacher_type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="teacher_qualification">Teacher Qualification</label>
                    <input type="text" class="form-control @error('teacher_qualification') is-invalid @enderror" id="teacher_qualification" name="teacher_qualification" value="{{ old('teacher_qualification', $teacher->teacher_qualification) }}">
                    @error('teacher_qualification')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="teacher_title">Teacher Title</label>
                    <input type="text" class="form-control @error('teacher_title') is-invalid @enderror" id="teacher_title" name="teacher_title" value="{{ old('teacher_title', $teacher->teacher_title) }}">
                    @error('teacher_title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="office_hours">Office Hours</label>
                    <input type="text" class="form-control @error('office_hours') is-invalid @enderror" id="office_hours" name="office_hours" value="{{ old('office_hours', $teacher->office_hours) }}">
                    @error('office_hours')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="office_address">Office Address</label>
                    <input type="text" class="form-control @error('office_address') is-invalid @enderror" id="office_address" name="office_address" value="{{ old('office_address', $teacher->office_address) }}">
                    @error('office_address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="biography">Biography</label>
                    <textarea class="form-control @error('biography') is-invalid @enderror" id="biography" name="biography" rows="3">{{ old('biography', $teacher->biography) }}</textarea>
                    @error('biography')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="certifications">Certifications (comma-separated)</label>
                    <input type="text" class="form-control @error('certifications') is-invalid @enderror" id="certifications" name="certifications" value="{{ old('certifications', is_array($teacher->certifications) ? implode(', ', $teacher->certifications) : $teacher->certifications) }}">
                    @error('certifications')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="publications">Publications (comma-separated)</label>
                    <input type="text" class="form-control @error('publications') is-invalid @enderror" id="publications" name="publications" value="{{ old('publications', is_array($teacher->publications) ? implode(', ', $teacher->publications) : $teacher->publications) }}">
                    @error('publications')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="number_of_awards">Number of Awards</label>
                    <input type="number" class="form-control @error('number_of_awards') is-invalid @enderror" id="number_of_awards" name="number_of_awards" value="{{ old('number_of_awards', $teacher->number_of_awards) }}">
                    @error('number_of_awards')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="employment_id">Employment ID</label>
                    <input type="text" class="form-control @error('employment_id') is-invalid @enderror" id="employment_id" name="employment_id" value="{{ old('employment_id', $teacher->employment_id) }}">
                    @error('employment_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="form-group">
                    <label for="date_of_employment">Date of Employment</label>
                    <input type="date" class="form-control @error('date_of_employment') is-invalid @enderror" id="date_of_employment" name="date_of_employment" value="{{ old('date_of_employment', $teacher->date_of_employment) }}">
                    @error('date_of_employment')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $teacher->address) }}">
                    @error('address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nationality">Nationality</label>
                    <input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', $teacher->nationality) }}">
                    @error('nationality')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="level">Level</label>
                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level">
                        <option value="">Select Level</option>
                        <option value="Senior Lecturer" {{ old('level', $teacher->level) == 'Senior Lecturer' ? 'selected' : '' }}>Senior Lecturer</option>
                        <option value="Junior Lecturer" {{ old('level', $teacher->level) == 'Junior Lecturer' ? 'selected' : '' }}>Junior Lecturer</option>
                        <option value="Technician" {{ old('level', $teacher->level) == 'Technician' ? 'selected' : '' }}>Technician</option>
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
    // Add any necessary JavaScript here
</script>
@endsection