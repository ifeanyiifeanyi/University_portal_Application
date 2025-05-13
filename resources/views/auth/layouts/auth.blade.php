<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('nursinglogo.webp') }}" type="image/png" />
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Plugins CSS -->
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

    <title>{{ config('app.name') }} - @yield('title')</title>

    <style>
        :root {
            --primary-color: #204939;
            --primary-light: #2a5e49;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --text-muted: #6c757d;
            --border-radius: 10px;
            --box-shadow: 0 8px 24px rgba(32, 73, 57, 0.12);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .bg-login {
            background-size: cover;
            background-position: center;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(32, 73, 57, 0.18);
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-content {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .auth-logo {
            margin-bottom: 2rem;
            transform-origin: center;
            animation: pulse 2s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }

        .auth-card-body {
            padding: 2.5rem;
        }

        .auth-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        .auth-form .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-color);
        }

        .auth-form .form-control {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: all var(--transition-speed);
            background-color: #f8f9fa;
        }

        .auth-form .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(32, 73, 57, 0.25);
            border-color: var(--primary-color);
            background-color: #fff;
        }

        .auth-form .input-group-text {
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            color: var(--text-muted);
            transition: all var(--transition-speed);
        }

        .auth-form .input-group-text:hover {
            background-color: var(--primary-light);
            color: white;
            border-color: var(--primary-light);
        }

        .auth-btn {
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all var(--transition-speed);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .auth-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .auth-btn:active {
            transform: translateY(1px);
        }

        .auth-btn::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: -100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s;
        }

        .auth-btn:hover::after {
            left: 100%;
        }

        .auth-link {
            color: var(--primary-color);
            font-weight: 600;
            transition: all var(--transition-speed);
            text-decoration: none;
        }

        .auth-link:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-switch .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(32, 73, 57, 0.25);
            border-color: var(--primary-color);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .auth-divider span {
            padding: 0 1rem;
            color: var(--text-muted);
        }

        .auth-divider:before,
        .auth-divider:after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }

        .text-danger {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .auth-card-body {
                padding: 1.5rem;
            }
        }

        /* Animation classes */
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
    </style>
    @yield('css')
</head>

<body class="bg-login">
    <div class="auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="text-center auth-logo" data-aos="zoom-in">
                        <img src="{{ asset('nursinglogo.webp') }}" alt="Logo" width="80" class="img-fluid" />
                    </div>

                    <div class="card" data-aos="fade-up">
                        <div class="card-body auth-card-body">
                            @yield('auth')
                        </div>
                    </div>

                    <div class="text-center mt-4 text-muted" data-aos="fade-up" data-aos-delay="300">
                        <small>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script>
        // Initialize AOS animation library
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Password show/hide functionality
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("fa-eye-slash");
                    $('#show_hide_password i').removeClass("fa-eye");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("fa-eye-slash");
                    $('#show_hide_password i').addClass("fa-eye");
                }
            });

            // Form input animation
            $('.form-control').focus(function() {
                $(this).parent().addClass('focused');
            }).blur(function() {
                if ($(this).val() === '') {
                    $(this).parent().removeClass('focused');
                }
            });

            // Check if inputs have values on page load
            $('.form-control').each(function() {
                if ($(this).val() !== '') {
                    $(this).parent().addClass('focused');
                }
            });
        });
    </script>

    @yield('js')
</body>
</html>
