<!-- Favicons -->
<link href="{{ asset('logo.png') }}" rel="icon">
<link href="{{ asset('logo.png') }}" rel="apple-touch-icon">

<!-- Google Fonts -->
<link href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- Vendor CSS Files -->
<link href="{{asset('teacher/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('teacher/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
<link href="{{asset('teacher/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
<link href="{{asset('teacher/vendor/quill/quill.snow.css')}}" rel="stylesheet">
<link href="{{asset('teacher/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
<link href="{{asset('teacher/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
<link href="{{asset('teacher/vendor/simple-datatables/style.css')}}" rel="stylesheet">

<!-- Template Main CSS File -->
<link href="{{asset('teacher/css/style.css')}}" rel="stylesheet">
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