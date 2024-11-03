  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar" style="background: #AE152D; color: #ffffff">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="{{route('teacher.view.dashboard')}}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->


      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('teacher.view.profile')}}">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->
      {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('teacher.view.departments')}}">
          <i class="bi bi-person"></i>
          <span>Departments</span>
        </a>
      </li><!-- End departments Page Nav --> --}}
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('teacher.view.courses')}}">
          <i class="bi bi-person"></i>
          <span>Courses assigned</span>
        </a>
      </li><!-- End courses Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('teacher.view.attendance')}}">
          <i class="bi bi-person"></i>
          <span>Attendance</span>
        </a>
      </li><!-- End courses Page Nav -->

  

    </ul>

  </aside><!-- End Sidebar-->