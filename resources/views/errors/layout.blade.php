<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') | {{ config('app.name') }}</title>
    <meta  name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .error-container {
            width: 100%;
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .error-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem auto;
            padding: 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-btn {
            margin: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .error-icon {
                width: 80px;
                height: 80px;
                padding: 1rem;
            }
            h1 {
                font-size: 1.5rem !important;
            }
            .btn-container {
                flex-direction: column;
                align-items: stretch;
            }
            .error-btn {
                width: 100%;
                margin: 5px 0;
            }
        }

        @media (min-width: 576px) {
            .btn-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-6">
                <div class="error-container text-center">
                    <div class="error-icon @yield('icon-bg')">
                        @yield('icon')
                    </div>

                    <h1 class="fw-bold mb-2">@yield('code') | @yield('title')</h1>
                    <p class="text-muted mb-4">@yield('message')</p>

                    <div class="btn-container">
                        <a href="{{ url('/') }}" class="btn btn-primary error-btn">
                            <i class="fas fa-home me-2"></i> Home
                        </a>

                        @yield('logout-button')

                        @if(url()->previous() !== url()->current())
                            <a href="{{ url()->previous() }}" class="btn btn-secondary error-btn">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
