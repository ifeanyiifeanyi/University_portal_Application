<!-- Left Sidebar Start -->
<div class="app-sidebar-menu" style="background: #fff; color : #444">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a href="{{route('student.view.dashboard')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('nursinglogo.webp') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('nursinglogo.webp') }}" alt="" height="24">
                    </span>
                </a>
                <a href="{{route('student.view.dashboard')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('nursinglogo.webp') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('nursinglogo.webp') }}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{route('student.view.dashboard')}}" class="text-dark nav-link {{ request()->routeIs('student.view.dashboard') ? 'nav-active' : '' }}">
                        <i data-feather="settings"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#paymentFinance" data-bs-toggle="collapse" class="text-dark nav-link">
                        <i data-feather="dollar-sign"></i>
                        <span>Payments & Finances</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse show" id="paymentFinance">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{route('student.view.fees.all')}}" class="{{ request()->routeIs('student.view.fees.all') ? 'nav-active' : '' }}">
                                    <i data-feather="tag"></i>
                                    <span>Pay Fees</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.payments')}}" class="{{ request()->routeIs('student.view.payments') ? 'nav-active' : '' }}">
                                    <i data-feather="credit-card"></i>
                                    <span>Payment History</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.recurring.payments')}}" class="{{ request()->routeIs('student.recurring.payments') ? 'nav-active' : '' }}">
                                    <i data-feather="repeat"></i>
                                    <span>Feeding Fee payments</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#academics" data-bs-toggle="collapse" class="text-dark nav-link">
                        <i data-feather="book-open"></i>
                        <span>Academics</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse show" id="academics">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{route('student.view.courseregistration')}}" class="{{ request()->routeIs('student.view.courseregistration') ? 'nav-active' : '' }}">
                                    <i data-feather="book"></i>
                                    <span>Course Registration</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.view.result.select') }}" class="{{ request()->routeIs('student.view.result.select') ? 'nav-active' : '' }}">
                                    <i data-feather="clipboard"></i>
                                    <span>Results</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('student.studymaterials.courses') }}" class="{{ request()->routeIs('student.studymaterials.courses') ? 'nav-active' : '' }}">
                                    <i data-feather="file-text"></i>
                                    <span>Study Materials</span>
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ route('student.view.support-tickets') }}" class="text-dark nav-link {{ request()->routeIs('student.view.support-tickets') ? 'nav-active' : '' }}">
                        <i data-feather="help-circle"></i>
                        <span>Support Ticket</span>
                    </a>
                </li>

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
<!-- Left Sidebar End -->
