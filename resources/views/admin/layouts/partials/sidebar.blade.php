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
    jj
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-category"></i>
                </div>
                <div class="menu-title">App Manager</div>
            </a>

            <ul>
                @can('manage academic sessions')
                    <li class="active"> <a href="{{ route('admin.academic.session') }}"><i
                                class="bx bx-right-arrow-alt"></i>Manage Academic Sessions</a>
                    </li>
                @endcan

                @can('manage semester')
                    <li>
                        <a href="{{ route('semester-manager.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                            Semester</a>
                    </li>
                @endcan
                @can('manage departments')
                    <li>
                        <a href="{{ route('admin.programs.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                            Programs</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.department.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                            Department</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.department.credit.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                            Department Credits</a>
                    </li>
                @endcan

                @can('manage faculties')
                    <li> <a href="{{ route('faculty-manager.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                            Faculties</a>
                    </li>
                @endcan


                @can('manage courses')
                    <li> <a href="{{ route('admin.courses.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                            Courses</a>
                    </li>
                @endcan

            </ul>
        </li>

        @canany(['assign semester courses to department', 'assign department courses to lecturers'])

            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bxs-spreadsheet"></i>
                    </div>
                    <div class="menu-title">Course Managment</div>
                </a>
                <ul>
                    @can('assign semester courses to department')
                        <li> <a href="{{ route('course-assignments.index') }}"><i class="bx bx-right-arrow-alt"></i>
                                Assign Semester Courses to Department</a>
                        </li>
                    @endcan

                    @can('assign department courses to lecturers')
                        <li> <a href="{{ route('admin.teacher.assignment.view') }}"><i class="bx bx-right-arrow-alt"></i>
                                Assign Department and Courses to Lecturers</a>
                        </li>
                    @endcan

                    @can('manage student course registrations')
                        <li>
                            <a href="{{ route('admin.course_registration.student_list') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Students Course Registration</a>
                        </li>
                    @endcan


                </ul>
            </li>
        @endcanany

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-task"></i>
                </div>
                <div class="menu-title">Course Registrations</div>
            </a>
            <ul>
                @can('manage student course registrations')
                    <li> <a href="{{ route('admin.students.all-course-registrations') }}"><i
                                class="bx bx-right-arrow-alt"></i>Manage Student Course Registrations</a>
                    </li>
                @endcan
            </ul>
        </li>

        @canany(['view timetable', 'view draft timetable'])
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-calendar-alt'></i>
                    </div>
                    <div class="menu-title">TimeTable Manager</div>
                </a>
                <ul>
                    @can('view timetable')
                        <li> <a href="{{ route('admin.timetable.view') }}"><i class="bx bx-right-arrow-alt"></i>TimeTable</a>
                        </li>
                    @endcan
                    @can('view timetable')
                        <li> <a href="{{ route('admin.timetable.draftIndex') }}"><i class="bx bx-right-arrow-alt"></i>Draft
                                TimeTable</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['manage payment types', 'manage payment methods', 'view invoice manager', 'pay fees'])
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bxs-credit-card-alt'></i>
                    </div>
                    <div class="menu-title">Payment Manager</div>
                </a>
                <ul>
                    @can('paid fee manager')
                        <li> <a href="{{ route('admin.payments.ProcessedPayments') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Paid Fees Manager</a>
                        </li>
                    @endcan

                    @can('paid receipt manager')
                        <li> <a href="{{ route('admin.payments.paidReceipts') }}"><i class="bx bx-right-arrow-alt"></i>Paid
                                Receipts</a>
                        </li>
                    @endcan

                    @can('view subaccount payments')
                        <li> <a href="{{ route('admin.payments.getSubaccountTransactions') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Subaccount
                                Payments</a>
                        </li>
                    @endcan
                    @can('view invoice manager')
                        <li> <a href="{{ route('admin.invoice.view') }}"><i class="bx bx-right-arrow-alt"></i>Invoice
                                Manager</a>
                        </li>
                    @endcan


                    @can('manage payment types')
                        <li> <a href="{{ route('admin.payment_type.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                                Payment Types</a>
                        </li>
                    @endcan
                    @can('installment payment config')
                        <li> <a href="{{ route('admin.installment-config.index') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Installment
                                Payment Config</a>
                        </li>
                    @endcan

                    @can('paid installment manager')
                        <li> <a href="{{ route('admin.installment_paid.index') }}"><i class="bx bx-right-arrow-alt"></i>Paid
                                Installments</a>
                        </li>
                    @endcan

                    @can('manage payment methods')
                        <li> <a href="{{ route('admin.payment_method.index') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                                Payment Methods</a>
                        </li>
                    @endcan

                    @can('pay fees')
                        <li> <a href="{{ route('admin.payment.pay') }}"><i class="bx bx-right-arrow-alt"></i>Pay Fees</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['manage lecturers', 'manage students'])
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-user'></i>
                    </div>
                    <div class="menu-title">Academic Profiles</div>
                </a>
                <ul>
                    @can('manage lecturers')
                        <li> <a href="{{ route('admin.teacher.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                                Lecturers</a>
                        </li>
                    @endcan
                    @can('manage students')
                        <li> <a href="{{ route('admin.student.view') }}"><i class="bx bx-right-arrow-alt"></i>Manage
                                Students</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        @canany(['view score', 'approve score', 'reject score', 'audit score', 'academic records'])
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bxs-hdd"></i>
                    </div>
                    <div class="menu-title">Academic Records</div>
                </a>
                <ul>
                    @can('view scores')
                        <li> <a href="{{ route('admin.score.approval.view') }}"><i class="bx bx-right-arrow-alt"></i>Submitted
                                Students Scores</a>
                        </li>
                    @endcan

                    @can('approve scores')
                        <li> <a href="{{ route('admin.approved_scores.view') }}"><i class="bx bx-right-arrow-alt"></i>Approved
                                Students Assessment score</a>
                        </li>
                    @endcan

                    @can('reject scores')
                        <li> <a href="{{ route('admin.score.rejected.view') }}"><i class="bx bx-right-arrow-alt"></i>Rejected
                                Students Assessment score</a>
                        </li>
                    @endcan

                    @can('audit scores')
                        <li> <a href="{{ route('admin.score.audit.view') }}"><i class="bx bx-right-arrow-alt"></i>Student
                                Score
                                Auditor</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['manage notifications', 'view notifications'])
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
        @endcanany

        @can('administrative access control')
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-lock'></i></div>
                    <div class="menu-title">Roles & Permissions</div>
                </a>
                <ul>
                    @can('manage roles')
                        <li>
                            <a href="{{ route('admin.roles.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>Manage Roles
                            </a>
                        </li>
                    @endcan
                    @can('manage permissions')
                        <li>
                            <a href="{{ route('admin.permissions.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>Manage Permissions
                            </a>
                        </li>
                    @endcan
                    @can('assign roles')
                        <li>
                            <a href="{{ route('admin.admin-users.roles') }}">
                                <i class="bx bx-right-arrow-alt"></i>Assign Roles
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan





        @can('view administrators')
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon"><i class="bx bx-archive"></i>
                    </div>
                    <div class="menu-title">Administrators</div>
                </a>
                <ul class="mm-collapse">
                    @can('view administrative members')
                        <li> <a href="{{ route('admin.accounts.managers.view') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Members</a>
                        </li>
                    @endcan
                    @can('create administrator')
                        <li> <a href="{{ route('admin.accounts.managers.create') }}"><i
                                    class="bx bx-right-arrow-alt"></i>Create Member</a>
                        </li>
                    @endcan
            </li>
        @endcan

    </ul>

    <!--end navigation-->
</div>
