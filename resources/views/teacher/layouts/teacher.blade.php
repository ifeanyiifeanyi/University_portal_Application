<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>{{config('app.name')}} - @yield('title')</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script defer>
    function calculateTotalAndGrade(rowNumber) {
        const assessmentScore = parseFloat(document.getElementById(`assessment${rowNumber}`).value) || 0;
        const examScore = parseFloat(document.getElementById(`exam${rowNumber}`).value) || 0;
        const total = assessmentScore + examScore;
  
        // Fetch the CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
        // Call the Laravel backend
        fetch({{route('calculatetotalgrade')}}, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ total: total })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById(`total${rowNumber}`).value = total;
            document.getElementById(`grade${rowNumber}`).value = data.grade;
        })
        .catch(error => console.error('Error:', error));
    }
  </script>
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
</body>

</html>