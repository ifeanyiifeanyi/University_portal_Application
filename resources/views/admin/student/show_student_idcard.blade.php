<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $student->user->full_name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#53cd1d">
    <meta name="apple-mobile-web-app-status-bar-style" content="#53cd1d">
    <meta name="msapplication-navbutton-color" content="#53cd1d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-title" content="Lab">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="msapplication-TileColor" content="#53cd1d">
    <meta name="msapplication-TileImage" content="{{ asset('nursinglogo.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('nursinglogo.webp') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('nursinglogo.webp') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('nursinglogo.webp') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#53cd1d">
    <meta name="apple-mobile-web-app-title" content="Lab">
    <meta name="application-name" content="Lab">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    <meta name="theme-color" content="#53cd1d">

</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <!-- Profile Image Column -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="{{ $student->user->profile_photo }}" alt="student"
                             class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover">

                        <h5 class="mb-2">{{ $student->user->full_name }}</h5>
                        <p class="text-muted mb-4">{{ $student->matric_number }}</p>
                        <p class="text-muted mb-1">{{ $student->department->name }}</p>
                        <p class="text-muted">{{ $student->faculty?->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Details Column -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Level</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->department->getDisplayLevel($student->current_level) }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">JAMB Number</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->jamb_registration_number }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Admission Year</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->year_of_admission }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Entry Mode</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->mode_of_entry }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Blood Group</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->blood_group }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Date of Birth</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->date_of_birth->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Contact</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">
                                    Phone: {{ $student->user->phone }}<br>
                                    Email: {{ $student->user->email }}
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Residential Address</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->residential_address }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="fw-bold mb-0">Permanent Address</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $student->permanent_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
