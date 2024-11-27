<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('nursinglogo.webp') }}" class="logo-icon" alt="logo icon">
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

                <li>
                    <a href="{{ route('admin.programs.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Programs</a>
                </li>

                <li> <a href="{{ route('faculty-manager.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Faculties</a>
                </li>
                <li>
                    <a href="{{ route('admin.department.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Department</a>
                </li>
                <li>
                    <a href="{{ route('admin.department.credit.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Department Credits</a>
                </li>
                <li> <a href="{{ route('admin.courses.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Courses</a>
                </li>

            </ul>
        </li>


        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bxs-spreadsheet"></i>
                </div>
                <div class="menu-title">Course Managment</div>
            </a>
            <ul>
                <li> <a href="{{ route('course-assignments.index') }}"><i class="bx bx-right-arrow-alt"></i>
                        Assign Semester Courses to Department</a>
                </li>

                <li> <a href="{{ route('admin.teacher.assignment.view') }}"><i class="bx bx-right-arrow-alt"></i>
                        Assign Department and Courses to Lecturers</a>
                </li>
                <li>
                    <a href="{{ route('admin.course_registration.student_list') }}"><i
                            class="bx bx-right-arrow-alt"></i>Students Course Registration</a>
                </li>


            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-task"></i>
                </div>
                <div class="menu-title">Course Registrations</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.students.all-course-registrations') }}"><i class="bx bx-right-arrow-alt"></i>Manage Student Course Registrations</a>
                </li>
            </ul>
        </li>







        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-calendar-alt'></i>
                </div>
                <div class="menu-title">TimeTable Manager</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.timetable.view') }}"><i class="bx bx-right-arrow-alt"></i>TimeTable</a>
                </li>
                <li> <a href="{{ route('admin.timetable.draftIndex') }}"><i class="bx bx-right-arrow-alt"></i>Draft
                        TimeTable</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bxs-credit-card-alt'></i>
                </div>
                <div class="menu-title">Payment Manager</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.payment_type.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Payment Types</a>
                </li>
                <li> <a href="{{ route('admin.payment_method.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                        Payment Methods</a>
                </li>
                <li> <a href="{{ route('admin.invoice.view') }}"><i class="bx bx-right-arrow-alt"></i>Invoice
                        Manager</a>
                </li>
                <li> <a href="{{ route('admin.payment.pay') }}">
                        <i class="bx bx-right-arrow-alt"></i>Pay Fees</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-user'></i>
                </div>
                <div class="menu-title">Academic Profiles</div>
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
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bxs-hdd"></i>
                </div>
                <div class="menu-title">Academic Records</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.score.approval.view') }}"><i class="bx bx-right-arrow-alt"></i>Submitted
                        Students Scores</a>
                </li>
                <li> <a href="{{ route('admin.approved_scores.view') }}"><i class="bx bx-right-arrow-alt"></i>Approved
                        Students Assessment score</a>
                </li>
                <li> <a href="{{ route('admin.score.rejected.view') }}"><i class="bx bx-right-arrow-alt"></i>Rejected
                        Students Assessment score</a>
                </li>
                <li> <a href="{{ route('admin.score.audit.view') }}"><i class="bx bx-right-arrow-alt"></i>Student Score
                        Auditor</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-bell'></i>
                </div>
                <div class="menu-title">Notifications</div>
            </a>
            <ul>
                <li> <a href="{{ route('admin.notification.view') }}"><i class="bx bx-right-arrow-alt"></i>Unread
                        Notifications</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-lock'></i></div>
                <div class="menu-title">Roles & Permissions</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.roles.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>Manage Roles
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.permissions.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>Manage Permissions
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.admin-users.roles') }}">
                        <i class="bx bx-right-arrow-alt"></i>Assign Roles
                    </a>
                </li>
            </ul>
        </li>






        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-archive"></i>
                </div>
                <div class="menu-title">Administrators</div>
            </a>
            <ul class="mm-collapse">
                <li> <a href="{{ route('admin.accounts.managers.view') }}"><i
                            class="bx bx-right-arrow-alt"></i>Members</a>
                </li>
                <li> <a href="{{ route('admin.accounts.managers.create') }}"><i
                            class="bx bx-right-arrow-alt"></i>Create Member</a>
                </li>
        </li>


    </ul>

    <!--end navigation-->
</div>
