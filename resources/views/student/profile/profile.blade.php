@extends('student.layouts.student')

@section('title', 'Student Profile')
@section('student')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.4.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />

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

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
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

        .profile-avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            transition: all 0.3s ease;
            box-shadow: var(--box-shadow-lg);
        }

        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--success-color);
            border: 3px solid white;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .avatar-edit-btn:hover {
            background: #157347;
            transform: scale(1.1);
        }

        .info-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow-lg);
        }

        .info-card-header {
            background: var(--primary-light);
            border-bottom: 2px solid var(--primary-color);
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .info-card-body {
            padding: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            flex: 0 0 40%;
        }

        .info-value {
            color: #212529;
            flex: 1;
            text-align: right;
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

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 56, 46, 0.25);
        }

        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--box-shadow-lg);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .btn-close {
            filter: invert(1);
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
            margin: 1rem 0;
        }

        .image-preview {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            border: 2px dashed var(--primary-color);
        }
         .edit-profile-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .edit-profile-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .file-drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-drop-zone:hover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .file-drop-zone.dragover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        /* Cropper.js custom styles */
        .cropper-container {
            max-height: 400px;
            margin: 1rem 0;
        }

        .crop-controls {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .crop-controls .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .cropped-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            margin: 1rem auto;
            overflow: hidden;
            display: none;
        }

        .cropped-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .profile-header {
                text-align: center;
                padding: 1.5rem;
            }
             .edit-profile-btn {
                margin-top: 1rem;
                width: 100%;
                text-align: center;
            }
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .info-label {
                flex: none;
            }

            .info-value {
                text-align: left;
            }

            .crop-controls {
                justify-content: center;
            }

            .crop-controls .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="container-fluid mt-3">
        <!-- Breadcrumb -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--primary-color);">Student Profile</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('student.view.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </nav>
        </div>

        @if ($student)
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center text-md-start">
                        <div class="profile-avatar-container">
                            <img src="{{ $student->user->profileImage() }}" alt="Profile Picture" class="profile-avatar"
                                id="currentProfileImage">
                            <div class="avatar-edit-btn" data-bs-toggle="modal" data-bs-target="#profileImageModal"
                                title="Update Profile Picture">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-2">{{ $student->user->first_name }} {{ $student->user->last_name }}
                            {{ $student->user->other_name }}</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fas fa-id-card me-2"></i>{{ $student->matric_number }}</p>
                                <p class="mb-1"><i class="fas fa-graduation-cap me-2"></i>{{ $student->department->name }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i
                                        class="fas fa-layer-group me-2"></i>{{ $student->department->getDisplayLevel($student->current_level) }}
                                </p>
                                <p class="mb-1"><i class="fas fa-calendar me-2"></i>{{ $student->year_of_admission }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center text-md-end mt-3">
                        <a href="{{ route('student.edit.profile') }}" class="edit-profile-btn">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>


            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </div>
                                <div class="info-card-body">
                                    <div class="info-row">
                                        <span class="info-label">JAMB Reg. Number:</span>
                                        <span
                                            class="info-value">{{ $student->jamb_registration_number ?: 'Not provided' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Date of Birth:</span>
                                        <span
                                            class="info-value">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'Not provided' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Gender:</span>
                                        <span class="info-value">{{ $student->gender ?: 'Not provided' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone:</span>
                                        <span class="info-value">{{ $student->user->phone ?: 'Not provided' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email:</span>
                                        <span class="info-value">{{ $student->user->email }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Marital Status:</span>
                                        <span class="info-value">{{ $student->marital_status ?: 'Not provided' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Religion:</span>
                                        <span class="info-value">{{ $student->religion ?: 'Not provided' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information -->
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-heartbeat me-2"></i>Medical Information
                                </div>
                                <div class="info-card-body">
                                    <div class="info-row">
                                        <span class="info-label">Blood Group:</span>
                                        <span class="info-value">{{ $student->blood_group ?: 'Not provided' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Genotype:</span>
                                        <span class="info-value">{{ $student->genotype ?: 'Not provided' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-book me-2"></i>Academic Information
                                </div>
                                <div class="info-card-body">
                                    <div class="info-row">
                                        <span class="info-label">Department:</span>
                                        <span class="info-value">{{ $student->department->name }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Current Level:</span>
                                        <span
                                            class="info-value">{{ $student->department->getDisplayLevel($student->current_level) }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Year of Admission:</span>
                                        <span class="info-value">{{ $student->year_of_admission }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Mode of Entry:</span>
                                        <span class="info-value">{{ $student->mode_of_entry }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                </div>
                                <div class="info-card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Nationality:</span>
                                                <span
                                                    class="info-value">{{ $student->nationality ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">State of Origin:</span>
                                                <span
                                                    class="info-value">{{ $student->state_of_origin ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">LGA/Province:</span>
                                                <span
                                                    class="info-value">{{ $student->lga_of_origin ?: 'Not provided' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Hometown:</span>
                                                <span class="info-value">{{ $student->hometown ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Residential Address:</span>
                                                <span
                                                    class="info-value">{{ $student->residential_address ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Permanent Address:</span>
                                                <span
                                                    class="info-value">{{ $student->permanent_address ?: 'Not provided' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Next of Kin Information -->
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-users me-2"></i>Next of Kin Information
                                </div>
                                <div class="info-card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Name:</span>
                                                <span
                                                    class="info-value">{{ $student->next_of_kin_name ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Relationship:</span>
                                                <span
                                                    class="info-value">{{ $student->next_of_kin_relationship ?: 'Not provided' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Phone:</span>
                                                <span
                                                    class="info-value">{{ $student->next_of_kin_phone ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Address:</span>
                                                <span
                                                    class="info-value">{{ $student->next_of_kin_address ?: 'Not provided' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Form -->
                {{-- <div class="col-lg-4">
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-edit me-2"></i>Update Profile
                        </div>
                        <div class="info-card-body">
                            @include('messages')
                            @include('student.profile.updateprofile')
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Profile Image Update Modal -->
            <div class="modal fade" id="profileImageModal" tabindex="-1" aria-labelledby="profileImageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="profileImageModalLabel">
                                <i class="fas fa-camera me-2"></i>Update Profile Picture
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('student.update.profile.image') }}" method="POST"
                            enctype="multipart/form-data" id="profileImageForm">
                            @csrf
                            <div class="modal-body">
                                <div class="file-drop-zone" id="fileDropZone">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="mb-2">Drag and drop your image here or click to select</p>
                                    <input type="file" id="profileImageInput" name="profile_photo" accept="image/*"
                                        class="d-none">
                                    <button type="button" class="btn btn-outline-primary"
                                        onclick="document.getElementById('profileImageInput').click()">
                                        <i class="fas fa-folder-open me-2"></i>Choose File
                                    </button>
                                </div>

                                <div class="image-preview-container d-none" id="imagePreviewContainer">
                                    <div class="text-center">
                                        <img id="imagePreview" class="image-preview" alt="Preview">

                                        <!-- Crop Controls -->
                                        <div class="crop-controls d-none" id="cropControls">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="cropBtn">
                                                <i class="fas fa-crop me-1"></i>Crop
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="zoomInBtn">
                                                <i class="fas fa-search-plus me-1"></i>Zoom In
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="zoomOutBtn">
                                                <i class="fas fa-search-minus me-1"></i>Zoom Out
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="rotateLeftBtn">
                                                <i class="fas fa-undo me-1"></i>Rotate Left
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="rotateRightBtn">
                                                <i class="fas fa-redo me-1"></i>Rotate Right
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="resetBtn">
                                                <i class="fas fa-refresh me-1"></i>Reset
                                            </button>
                                        </div>

                                        <!-- Cropped Preview -->
                                        <div class="cropped-preview" id="croppedPreview">
                                            <img id="croppedImage" alt="Cropped Preview">
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="removeImageBtn">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input for cropped image data -->
                                <input type="hidden" id="croppedImageData" name="cropped_image_data">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary-custom" id="uploadBtn" disabled>
                                    <i class="fas fa-upload me-2"></i>Update Picture
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileDropZone = document.getElementById('fileDropZone');
            const profileImageInput = document.getElementById('profileImageInput');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const cropControls = document.getElementById('cropControls');
            const croppedPreview = document.getElementById('croppedPreview');
            const croppedImage = document.getElementById('croppedImage');
            const croppedImageData = document.getElementById('croppedImageData');
            const uploadBtn = document.getElementById('uploadBtn');
            const removeImageBtn = document.getElementById('removeImageBtn');

            // Crop control buttons
            const cropBtn = document.getElementById('cropBtn');
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const rotateLeftBtn = document.getElementById('rotateLeftBtn');
            const rotateRightBtn = document.getElementById('rotateRightBtn');
            const resetBtn = document.getElementById('resetBtn');

            let cropper = null;

            // File drop functionality
            fileDropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileDropZone.classList.add('dragover');
            });

            fileDropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                fileDropZone.classList.remove('dragover');
            });

            fileDropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                fileDropZone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            profileImageInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            function handleFileSelect(file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        fileDropZone.classList.add('d-none');
                        imagePreviewContainer.classList.remove('d-none');

                        // Initialize cropper
                        initializeCropper();

                        uploadBtn.disabled = false;
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Please select a valid image file.');
                }
            }

            function initializeCropper() {
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(imagePreview, {
                    aspectRatio: 1, // Square aspect ratio for profile pictures
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    ready: function() {
                        cropControls.classList.remove('d-none');
                    }
                });
            }

            // Crop control events
            cropBtn.addEventListener('click', function() {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 300,
                        height: 300,
                        imageSmoothingEnabled: false,
                        imageSmoothingQuality: 'high',
                    });

                    // Show cropped preview
                    croppedImage.src = canvas.toDataURL();
                    croppedPreview.style.display = 'block';

                    // Store cropped image data
                    croppedImageData.value = canvas.toDataURL('image/jpeg', 0.9);
                }
            });

            zoomInBtn.addEventListener('click', function() {
                if (cropper) {
                    cropper.zoom(0.1);
                }
            });

            zoomOutBtn.addEventListener('click', function() {
                if (cropper) {
                    cropper.zoom(-0.1);
                }
            });

            rotateLeftBtn.addEventListener('click', function() {
                if (cropper) {
                    cropper.rotate(-90);
                }
            });

            rotateRightBtn.addEventListener('click', function() {
                if (cropper) {
                    cropper.rotate(90);
                }
            });

            resetBtn.addEventListener('click', function() {
                if (cropper) {
                    cropper.reset();
                    croppedPreview.style.display = 'none';
                    croppedImageData.value = '';
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                profileImageInput.value = '';
                croppedImageData.value = '';
                imagePreviewContainer.classList.add('d-none');
                fileDropZone.classList.remove('d-none');
                cropControls.classList.add('d-none');
                croppedPreview.style.display = 'none';
                uploadBtn.disabled = true;
            });

            // Form validation
            document.getElementById('profileImageForm').addEventListener('submit', function(e) {
                if (!profileImageInput.files.length && !croppedImageData.value) {
                    e.preventDefault();
                    alert('Please select and crop an image file.');
                }
            });

            // Clean up cropper when modal is closed
            document.getElementById('profileImageModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                // Reset form
                profileImageInput.value = '';
                croppedImageData.value = '';
                imagePreviewContainer.classList.add('d-none');
                fileDropZone.classList.remove('d-none');
                cropControls.classList.add('d-none');
                croppedPreview.style.display = 'none';
                uploadBtn.disabled = true;
            });
        });
    </script>
@endsection
