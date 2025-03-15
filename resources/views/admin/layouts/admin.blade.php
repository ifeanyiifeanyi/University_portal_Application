<!doctype html>
<html lang="en" class="light-theme">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('nursinglogo.webp') }}" type="image/png" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">

    <!--plugins-->
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />

    <!--plugins-->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />


    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/header-colors.css') }}" />

    <title>{{ config('app.name') }} - @yield('title')</title>

    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" />

    @yield('css')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    {{-- ckeditor  --}}
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.css">
    <style>
        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-container {
            text-align: center;
        }

        .loading-text {
            color: #0d6efd;
            font-weight: 500;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />

 <!--plugins-->
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />


 <!-- loader-->
 <link href="https://portal.stcharlesborromeocon.com/assets/css/pace.min.css" rel="stylesheet" />
 <!-- Bootstrap CSS -->
 <link href="https://portal.stcharlesborromeocon.com/assets/css/bootstrap.min.css" rel="stylesheet">
 <link href="https://portal.stcharlesborromeocon.com/assets/css/app.css" rel="stylesheet">
 <link href="https://portal.stcharlesborromeocon.com/assets/css/icons.css" rel="stylesheet">
 <!-- Theme Style CSS -->
 <link rel="stylesheet" href="https://portal.stcharlesborromeocon.com/assets/css/dark-theme.css" />
 <link rel="stylesheet" href="https://portal.stcharlesborromeocon.com/assets/css/semi-dark.css" />
 <link rel="stylesheet" href="https://portal.stcharlesborromeocon.com/assets/css/header-colors.css" />

 <title>COLLEGE OF NURSING SCIENCES, ST CHARLES BORROMEO SPECIALIST HOSPITAL, ONITSHA. - Dashboard</title>

 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
 <link href="https://portal.stcharlesborromeocon.com/assets/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />
</head>


<body>

    <!-- Add this right after opening <body> tag -->
    <div id="loader-wrapper" style="display: none;">
        <div class="loader-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text mt-2">Please wait...</div>
        </div>
    </div>

    <!-- Your existing layout content -->
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->

        @include('admin.layouts.partials.sidebar')
        <!--end sidebar wrapper -->

        <!--start header -->
        @include('admin.layouts.partials.navbar')
        <!--end header -->


        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <!--breadcrumb-->
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">
                        <h3> @yield('title')</h3>
                    </div>
                    <div class="px-3">
                        <button onclick="goBack()" class="btn btn-primary d-flex align-items-center">
                            <i class="bx bx-left-arrow-alt me-1"></i>Back
                        </button>
                    </div>
                    <div class="ms-auto">

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary">Quick Links</button>
                            <button type="button"
                                class="btn btn-secondary split-bg-secondary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                                <a class="dropdown-item" href="{{ route('admin.view.dashboard') }}">Dashboard</a>
                                <a class="dropdown-item" href="{{ route('admin.department.view') }}">Departments</a>
                                <a class="dropdown-item" href="{{ route('admin.student.view') }}">Students</a>
                                <a class="dropdown-item" href="{{ route('admin.teacher.view') }}">Lecturers</a>
                                <a class="dropdown-item" href="{{ route('admin.timetable.view') }}">Timetable</a>
                                <div class="dropdown-divider"></div>
                                {{-- <a class="dropdown-item text-danger"
                                    href="{{ route('logout') }}">Logout</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!--end breadcrumb-->
                @yield('admin')


            </div>
        </div>
        <!--end page wrapper -->


        @include('admin.layouts.partials.footer')
    </div>
    <!--end wrapper-->



    <!--start switcher-->
    {{-- @include('admin.layouts.partials.switcher') --}}
    <!--end switcher-->

    <script src="{{ asset('') }}assets/js/jquery.min.js"></script>
    <script src="{{ asset('') }}assets/js/pace.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('') }}assets/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="{{ asset('') }}assets/plugins/chartjs/js/Chart.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="{{ asset('') }}assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/sparkline-charts/jquery.sparkline.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/jquery-knob/excanvas.js"></script>
    <script src="{{ asset('') }}assets/plugins/jquery-knob/jquery.knob.js"></script>
    <script>
        $(function() {
            $(".knob").knob();
        });
    </script>
    <script>
        function goBack() {
            if (document.referrer) {
                // If there's a previous page in history
                window.history.back();
            } else {
                // If no previous page, redirect to dashboard or default page
                window.location.href = "{{ route('admin.view.dashboard') }}";
            }
        }
    </script>

    <script src="{{ asset('') }}assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('') }}assets/plugins/select2/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>







    <script src="{{ asset('') }}assets/js/index.js"></script>
    <!--app JS-->
    <script src="{{ asset('') }}assets/js/app.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}"

            switch (type) {
                case 'info':
                    toastr.info(" {{ Session::get('message') }} ");
                    break;

                case 'success':
                    toastr.success(" {{ Session::get('message') }} ");
                    break;

                case 'warning':
                    toastr.warning(" {{ Session::get('message') }} ");
                    break;

                case 'error':
                    toastr.error(" {{ Session::get('message') }} ");
                    break;
            }
        @endif
    </script>
    <script>
        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        $('.multiple-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
    </script>
    @yield('javascript')
    <script src="{{ asset('') }}assets/plugins/fancy-file-uploader/jquery.ui.widget.js"></script>
    <script src="{{ asset('') }}assets/plugins/fancy-file-uploader/jquery.fileupload.js"></script>
    <script src="{{ asset('') }}assets/plugins/fancy-file-uploader/jquery.iframe-transport.js"></script>
    <script src="{{ asset('') }}assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js"></script>
    <script src="{{ asset('') }}assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js"></script>
    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.1/"
            }
        }
    </script>
    <script type="module">
        import {
            ClassicEditor,
            Essentials,
            Paragraph,
            Bold,
            Italic,
            Font,
            Link,
            BlockQuote,
            List,
            MediaEmbed,
            Autoformat,

        } from 'ckeditor5';

        ClassicEditor
            .create(document.querySelector('#editor'), {
                plugins: [Essentials, Paragraph, Bold, Italic, Font, Link, BlockQuote, List, MediaEmbed, Autoformat],
                toolbar: [
                    'undo', 'redo', '|', 'bold', 'italic', '|', 'blockQuote', 'link', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
                    'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript',
                    '|', 'bulletedList', 'numberedList', 'todoList', '|', 'outdent', 'indent',
                    '|', 'insertTable', 'mediaEmbed', 'imageUpload', 'blockQuote', 'codeBlock',
                ]
            })
            .then(editor => {
                window.editor = editor;
            })
            .catch(error => {
                console.error(error);
            });
    </script>
    <script src="{{ asset('') }}assets/plugins/select2/js/select2.min.js"></script>
    <script>
        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
        $('.multiple-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
    </script>

    <script>
        $(function() {
            $('[data-bs-toggle="popover"]').popover();
            $('[data-bs-toggle="tooltip"]').tooltip();
        })
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show loader on form submission
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    document.getElementById('loader-wrapper').style.display = 'flex';
                });
            });

            // Show loader on all AJAX requests
            let originalXHR = window.XMLHttpRequest;

            function newXHR() {
                let xhr = new originalXHR();
                xhr.addEventListener('loadstart', function() {
                    document.getElementById('loader-wrapper').style.display = 'flex';
                });
                xhr.addEventListener('loadend', function() {
                    document.getElementById('loader-wrapper').style.display = 'none';
                });
                return xhr;
            }
            window.XMLHttpRequest = newXHR;

            // If you're using axios or fetch, add these handlers
            if (window.axios) {
                axios.interceptors.request.use(function(config) {
                    document.getElementById('loader-wrapper').style.display = 'flex';
                    return config;
                });

                axios.interceptors.response.use(function(response) {
                    document.getElementById('loader-wrapper').style.display = 'none';
                    return response;
                }, function(error) {
                    document.getElementById('loader-wrapper').style.display = 'none';
                    return Promise.reject(error);
                });
            }
        });
    </script>
</body>

</html>
