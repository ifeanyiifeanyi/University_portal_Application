
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
                                <a href="{{route('student.view.dashboard')}}" class="text-dark">
                                    <i data-feather="settings"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{route('student.view.fees.all')}}" class="text-dark">
                                    <i data-feather="dollar-sign"></i>
                                    <span>Fees</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.courseregistration')}}" class="text-dark">
                                    <i data-feather="book"></i>
                                    <span>Course registration</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.result.select')}}" class="text-dark">
                                    <i data-feather="clipboard"></i>
                                    <span>Results</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.payments')}}" class="text-dark">
                                    <i data-feather="table"></i>
                                    <span>Payments</span>
                                </a>
                            </li>




                        </ul>

                    </div>
                    <!-- End Sidebar -->

                    <div class="clearfix"></div>

                </div>
            </div>
            <!-- Left Sidebar End -->
