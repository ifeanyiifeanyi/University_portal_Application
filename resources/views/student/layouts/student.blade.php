<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <meta name="author" content="Zoyothemes" />
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

        /* Initially show the preloader */
        body.loading #preloader {
            display: block;
        }

        /* Hide the preloader once the page is fully loaded */
        body:not(.loading) #preloader {
            display: none;
        }

        .nav-link {
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }

        .nav-active {
            border-bottom: 2px solid #007bff;
            color: #007bff;
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
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>
    <!-- END wrapper -->

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

            form.addEventListener('submit', function(event) {

                preloader.style.display = 'block';


                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
            });
        });
    </script>
    @yield('javascript')
</body>

</html>
