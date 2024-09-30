
            <!-- Left Sidebar Start -->
            <div class="app-sidebar-menu" style="background: #AE152D; color : #ffffff">
                <div class="h-100" data-simplebar>

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">

                        <div class="logo-box">
                            <a href="{{route('student.view.dashboard')}}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('logo.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('logo.png') }}" alt="" height="24">
                                </span>
                            </a>
                            <a href="{{route('student.view.dashboard')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('logo.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('logo.png') }}" alt="" height="24">
                                </span>
                            </a>
                        </div>

                        <ul id="side-menu">

                            <li class="menu-title">Menu</li>

                           

                            <li>
                                <a href="{{route('student.view.dashboard')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                           
                            <li>
                                <a href="{{route('student.view.fees.all')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Fees</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.courseregistration')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Course registration</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.result.select')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Results</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('student.view.payments')}}" class="text-white">
                                    <i data-feather="aperture"></i>
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