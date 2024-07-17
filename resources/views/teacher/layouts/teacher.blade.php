<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>{{config('app.name')}} - @yield('title')</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  @include('teacher.layouts.partials.navbar')
  @yield('css')
</head>

<body class="loading">

  @include('teacher.layouts.partials.header')
  @include('teacher.layouts.partials.sidebar')



  <main id="main" class="main">
     <!-- Preloader element -->
     <div id="preloader">Loading...</div>

    @yield('teacher')


</main><!-- End #main -->
@include('teacher.layouts.partials.footer')


</body>

</html>