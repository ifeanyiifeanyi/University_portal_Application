<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error | {{ config('app.name') }}</title>

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
            background-color: rgba(220, 53, 69, 0.25);
        }
        .error-details {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            max-height: 300px;
            overflow-y: auto;
            font-family: monospace;
            white-space: pre-wrap;
            text-align: left;
            font-size: 0.85rem;
        }
        .error-btn {
            margin: 5px;
        }
        .card-header {
            background-color: rgba(0, 0, 0, 0.03);
        }

        /* Make stack trace scrollable horizontally on small screens */
        .error-details {
            overflow-x: auto;
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
            .card-body {
                padding: 0.75rem;
            }
            .accordion-button {
                padding: 0.75rem;
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
            <div class="col-sm-11 col-md-10 col-lg-8">
                <div class="error-container">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-circle text-danger fs-1"></i>
                    </div>

                    <h1 class="fw-bold mb-2 text-center">Oops! Something went wrong</h1>
                    <p class="text-muted mb-4 text-center">We encountered an unexpected issue while processing your request.</p>

                    @if(app()->environment('local', 'development', 'staging') && isset($exception))
                    <div class="accordion mb-4" id="errorAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#errorDetails" aria-expanded="false" aria-controls="errorDetails">
                                    <i class="fas fa-bug me-2"></i> Error Details <small class="ms-2 text-muted">(Developer Only)</small>
                                </button>
                            </h2>
                            <div id="errorDetails" class="accordion-collapse collapse" data-bs-parent="#errorAccordion">
                                <div class="accordion-body p-0">
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <strong>Exception Type:</strong> {{ get_class($exception) }}
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-2"><strong>Message:</strong> {{ $exception->getMessage() }}</p>
                                            <p class="mb-0"><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
                                        </div>
                                    </div>

                                    <div class="error-details">
{{ $exception->getTraceAsString() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> This error has been logged and our technical team has been notified.
                    </div>

                    <div class="btn-container mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary error-btn">
                            <i class="fas fa-home me-2"></i> Home
                        </a>

                        @auth
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger error-btn">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        @endauth

                        @if(url()->previous() !== url()->current())
                            <a href="{{ url()->previous() }}" class="btn btn-secondary error-btn">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </a>
                        @endif

                        <button class="btn btn-outline-primary error-btn" onclick="window.location.reload()">
                            <i class="fas fa-sync me-2"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
