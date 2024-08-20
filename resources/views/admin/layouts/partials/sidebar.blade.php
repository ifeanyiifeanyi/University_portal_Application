<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('') }}assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">ADM</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-home-circle'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
            <ul>
                <li> <a href="index.html"><i class="bx bx-right-arrow-alt"></i>Default</a>
                </li>
                <li> <a href="dashboard-eCommerce.html"><i class="bx bx-right-arrow-alt"></i>eCommerce</a>
                </li>
                <li> <a href="dashboard-analytics.html"><i class="bx bx-right-arrow-alt"></i>Analytics</a>
                </li>
                <li> <a href="dashboard-digital-marketing.html"><i class="bx bx-right-arrow-alt"></i>Digital
                        Marketing</a>
                </li>
                <li> <a href="dashboard-human-resources.html"><i class="bx bx-right-arrow-alt"></i>Human
                        Resources</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-category"></i>
                </div>
                <div class="menu-title">App Manager</div>
            </a>
            <ul>
                <li class="active"> <a href="{{ route('admin.academic.session') }}"><i
                            class="bx bx-right-arrow-alt"></i>Manage Academic Sessions</a>
                </li>

                <li>
                    <a href="{{ route('semester-manager.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Semester</a>
                </li>

                <li> <a href="{{ route('faculty-manager.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Faculties</a>
                </li>
                <li>
                    <a href="{{ route('admin.department.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Department</a>
                </li>
                <li>
                    <a href="{{ route('admin.department.credit.view') }}"><i class="bx bx-right-arrow-alt"></i>Assign
                        Department Credits</a>
                </li>
                <li> <a href="{{ route('admin.courses.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage Courses</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-slider-alt'></i>
                </div>
                <div class="menu-title">Academics Manager</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.teacher.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Lecturers</a>
                </li>
                <li> <a href="{{ route('admin.student.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Students</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('course-assignments.index') }}">
                <div class="parent-icon"><i class="bx bx-folder"></i>
                </div>
                <div class="menu-title">Course | Semester Manager</div>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.teacher.assignment.view') }}">
                <div class="parent-icon"><i class="fadeIn animated bx bx-select-multiple"></i>
                </div>
                <div class="menu-title">Deparment | lecturer | Courses</div>
            </a>
        </li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-lock"></i>
                </div>
                <div class="menu-title">Administrators</div>
            </a>
            <ul class="mm-collapse">
                <li> <a href="{{ route('admin.accounts.managers.view') }}"><i class="bx bx-right-arrow-alt"></i>Members</a>
                </li>
                <li> <a href="{{ route('admin.accounts.managers.create') }}"><i class="bx bx-right-arrow-alt"></i>Create Member</a>
                </li>
                </li>
            </ul>
        </li>
    </ul>
    <!--end navigation-->
</div>
