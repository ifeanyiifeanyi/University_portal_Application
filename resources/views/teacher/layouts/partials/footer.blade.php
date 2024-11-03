<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>{{env('APP_NAME')}}</span></strong>. All Rights Reserved
    </div>
    
  </footer><!-- End Footer -->
  
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  
  <!-- Vendor JS Files -->
  <script src="{{asset('teacher/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{asset('teacher/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('teacher/vendor/chart.js/chart.umd.js')}}"></script>
  <script src="{{asset('teacher/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{asset('teacher/vendor/quill/quill.js')}}"></script>
  <script src="{{asset('teacher/vendor/simple-datatables/simple-datatables.js')}}"></script>
  <script src="{{asset('teacher/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('teacher/vendor/php-email-form/validate.js')}}"></script>
  
  <!-- Template Main JS File -->
  <script src="{{asset('teacher/js/main.js')}}"></script>
  <script src="{{asset('teacher/js/jquery-3.2.1.min.js')}}"></script>

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