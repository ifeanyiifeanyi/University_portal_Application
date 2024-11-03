<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8" />
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="@yield('title')"/>
        <meta name="author" content="Zoyothemes"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('logo.png') }}">

        <!-- App css -->
        <link href="{{asset('parent/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

        <!-- Icons -->
        <link href="{{asset('parent/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        @yield('css')
        <style>
            /* Basic styling for the preloader and greyed background */
            #preloader {
                display: none; /* Hidden by default */
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
        </style>

    </head>

    <!-- body start -->
    <body data-menu-color="dark" data-sidebar="default">
        <div id="preloader">Loading...</div>

        <!-- Begin page -->
        <div id="app-layout">


           
@include('parent.layouts.partials.topbar')
@include('parent.layouts.partials.sidebar')

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
         
            <div class="content-page">
                <div class="content">
                    @yield('parent')





                </div> <!-- content -->

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col fs-13 text-muted text-center">
                                &copy; <script>document.write(new Date().getFullYear())</script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!" class="text-reset fw-semibold">Zoyothemes</a> 
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
        <script src="{{asset('parent/assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('parent/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('parent/assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('parent/assets/libs/node-waves/waves.min.js')}}"></script>
        <script src="{{asset('parent/assets/libs/waypoints/lib/jquery.waypoints.min.js')}}"></script>
        <script src="{{asset('parent/assets/libs/jquery.counterup/jquery.counterup.min.js')}}"></script>
        <script src="{{asset('parent/assets/libs/feather-icons/feather.min.js')}}"></script>

        <!-- Apexcharts JS -->
        <script src="{{asset('parent/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

        <!-- for basic area chart -->
        <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>

        <!-- Widgets Init Js -->
        <script src="{{asset('parent/assets/js/pages/dashboard.init.js')}}"></script>

        <!-- App js-->
        <script src="{{asset('parent/assets/js/app.js')}}"></script>

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
        
    </body>
</html>