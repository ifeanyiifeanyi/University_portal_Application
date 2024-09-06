<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>{{config('app.name')}} - @yield('title')</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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

<script>
     $(document).ready(function(){
        $(document).on('click', '#addpub', function(){
          $('#displaypublications').append("<tr><td><div class='form-group'><input type='text' class='form-control' value='' name='publications[]'></div></td><td><button type='button' id='cancelpub' class='btn btn-danger'>X</button></td></tr>");
    });
    $(document).on('click','#cancelpub', function() {
      $(this).closest('tr').remove();
      });
  });


  $(document).ready(function(){
        $(document).on('click', '#addcert', function(){
          $('#displaycertificates').append("<tr><td><div class='form-group'><input type='text' class='form-control' name='certifications[]'></div></td><td><button type='button' id='cancelcert' class='btn btn-danger'>X</button></td></tr>");
    });
    $(document).on('click','#cancelcert', function() {
      $(this).closest('tr').remove();
      });
  });
</script>

@yield('javascript')
</body>

</html>