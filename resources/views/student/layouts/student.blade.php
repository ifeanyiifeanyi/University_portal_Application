<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name') }}" />
    <meta name="author" content="{{ config('app.name') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('nursinglogo.webp') }}">

    <!-- App css -->
    <link href="{{ asset('student/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="{{ asset('student/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @yield('css')
    <style>
        /* Basic styling for the preloader and greyed background */
        #preloader {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            text-align: center;
            padding-top: 20%;
            color: #fff;
            font-size: 1.5em;
        }

        /* Hide the preloader once the page is fully loaded */
        body:not(.loading) #preloader {
            display: none;
        }

        /* Basic styling for all nav links */
        .nav-link {
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }

        /* Ensuring the nested menu items have proper styling */
        .nav-second-level a {
            padding: 8px 15px 8px 30px;
            display: block;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        /* Hover state for all nav links including nested ones */
        .nav-link:hover,
        .nav-second-level a:hover {
            color: #ddd !important;
            background-color: rgba(0, 128, 128, 0.788) !important;
            transition: all 0.3s ease;
        }

        /* Active state for all nav links including nested ones */
        .nav-active,
        .nav-second-level a.nav-active {
            background-color: teal !important;
            color: #ddd !important;
        }

        /* Making sure active links maintain styling on hover */
        .nav-active:hover,
        .nav-second-level a.nav-active:hover {
            background-color: teal !important;
            color: #ddd !important;
        }

        /* Ensure proper spacing and visibility in the sidebar */
        .collapse.show {
            display: block;
        }

        .nav-second-level {
            padding-left: 0;
            list-style: none;
        }

        /* Add a slight indent to nested menu items */
        .nav-second-level li {
            margin-left: 0;
            position: relative;
        }

        /* Telegram Chat Support Button Container */
        .telegram-support-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Support Text Label */
        .telegram-support-text {
            background: rgba(0, 136, 204, 0.95);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            box-shadow: 0 3px 15px rgba(0, 136, 204, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }

        .telegram-support-text:hover {
            background: rgba(0, 136, 204, 1);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 136, 204, 0.5);
            color: white;
            text-decoration: none;
        }

        /* Telegram Chat Support Button Styles */
        .telegram-chat-button {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0088cc, #229ED9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 136, 204, 0.4);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .telegram-chat-button:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 25px rgba(0, 136, 204, 0.6);
            text-decoration: none;
        }

        .telegram-chat-button:active {
            transform: translateY(-1px) scale(1.02);
        }

        .telegram-icon {
            width: 32px;
            height: 32px;
            fill: white;
        }

        /* Tooltip for the button */
        .telegram-chat-button::before {
            content: 'Need Help? Chat with Support on Telegram';
            position: absolute;
            right: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
            max-width: 250px;
        }

        .telegram-chat-button::after {
            content: '';
            position: absolute;
            right: 60px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-left: 10px solid rgba(0, 0, 0, 0.9);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .telegram-chat-button:hover::before,
        .telegram-chat-button:hover::after {
            opacity: 1;
            visibility: visible;
        }

        /* Pulse animation for attention */
        @keyframes pulse {
            0% {
                box-shadow: 0 4px 20px rgba(0, 136, 204, 0.4);
            }
            50% {
                box-shadow: 0 4px 20px rgba(0, 136, 204, 0.8), 0 0 0 10px rgba(0, 136, 204, 0.1);
            }
            100% {
                box-shadow: 0 4px 20px rgba(0, 136, 204, 0.4);
            }
        }

        .telegram-support-container.pulse .telegram-chat-button {
            animation: pulse 2s infinite;
        }

        .telegram-support-container.pulse .telegram-support-text {
            animation: pulse 2s infinite;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .telegram-support-container {
                bottom: 15px;
                right: 15px;
                gap: 10px;
            }

            .telegram-chat-button {
                width: 55px;
                height: 55px;
            }

            .telegram-icon {
                width: 28px;
                height: 28px;
            }

            .telegram-support-text {
                font-size: 13px;
                padding: 6px 12px;
            }

            .telegram-chat-button::before {
                right: 65px;
                font-size: 11px;
                padding: 8px 12px;
                content: 'Chat Support on Telegram';
                max-width: 180px;
            }

            .telegram-chat-button::after {
                right: 55px;
            }
        }

        @media (max-width: 480px) {
            .telegram-support-container {
                bottom: 10px;
                right: 10px;
                flex-direction: column;
                align-items: flex-end;
                gap: 8px;
            }

            .telegram-chat-button {
                width: 50px;
                height: 50px;
            }

            .telegram-icon {
                width: 24px;
                height: 24px;
            }

            .telegram-support-text {
                font-size: 12px;
                padding: 5px 10px;
            }

            .telegram-chat-button::before {
                display: none; /* Hide tooltip on very small screens */
            }

            .telegram-chat-button::after {
                display: none;
            }
        }

        /* Ensure button doesn't interfere with sidebar on mobile */
        @media (max-width: 991px) {
            body.sidebar-enable .telegram-support-container {
                right: 15px;
            }
        }
    </style>

</head>

<!-- body start -->

<body data-menu-color="dark" data-sidebar="default">
    <div id="preloader">Loading...</div>

    <!-- Begin page -->
    <div id="app-layout">

        @include('student.layouts.partials.topbar')
        @include('student.layouts.partials.sidebar')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                @yield('student')
            </div> <!-- content -->

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col fs-13 text-muted text-center">
                            &copy;
                            <script>
                                document.write(new Date().getFullYear())
                            </script> <a href="{{ config('app.url') }}"
                                class="text-reset fw-semibold">{{ config('app.name') }}</a>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>
        <!-- END wrapper -->

    </div>
    <!-- END wrapper -->

    <!-- Telegram Support Chat Button -->
    <div class="telegram-support-container" id="telegramSupportContainer">
        <a href="https://t.me/+GXQNrUOse-RiNTE0" target="_blank" rel="noopener noreferrer" class="telegram-support-text">
            💬 Need Help? Chat Support
        </a>
        <a href="https://t.me/+GXQNrUOse-RiNTE0" target="_blank" rel="noopener noreferrer" class="telegram-chat-button" id="telegramSupport" title="Get instant help from our support team on Telegram">
            <svg class="telegram-icon" viewBox="0 0 24 24">
                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.568 8.16l-1.58 7.44c-.12.539-.432.672-.864.42l-2.388-1.764-1.152 1.116c-.128.128-.236.236-.48.236l.168-2.388 4.332-3.924c.192-.168-.036-.264-.3-.096L8.268 12.36l-2.304-.72c-.504-.156-.516-.504.108-.744L18.432 7.2c.42-.156.792.096.636.96z"/>
            </svg>
        </a>
    </div>

    <!-- Vendor -->
    <script src="{{ asset('student/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('student/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('student/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('student/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('student/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('student/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('student/assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- Apexcharts JS -->
    <script src="{{ asset('student/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- for basic area chart -->
    <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>

    <!-- Widgets Init Js -->
    <script src="{{ asset('student/assets/js/pages/dashboard.init.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <!-- App js-->
    <script src="{{ asset('student/assets/js/app.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const preloader = document.getElementById('preloader');
            const form = document.getElementById('requestForm');

            window.addEventListener('load', function() {
                document.body.classList.remove('loading');
            });

            if (form) {
                form.addEventListener('submit', function(event) {
                    preloader.style.display = 'block';
                    const submitButton = form.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                });
            }

            // Telegram support button functionality
            const telegramContainer = document.getElementById('telegramSupportContainer');
            const telegramButton = document.getElementById('telegramSupport');

            // Add pulse animation on first load to draw attention
            setTimeout(() => {
                telegramContainer.classList.add('pulse');
                setTimeout(() => {
                    telegramContainer.classList.remove('pulse');
                }, 4000); // Remove pulse after 4 seconds
            }, 2000); // Start pulse after 2 seconds of page load

            // Track clicks for analytics (optional)
            telegramButton.addEventListener('click', function() {
                // You can add analytics tracking here if needed
                console.log('Telegram support chat opened');
            });
        });
    </script>
    @yield('javascript')
</body>

</html>
