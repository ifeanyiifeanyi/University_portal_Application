
            <!-- Left Sidebar Start -->
            <div class="app-sidebar-menu" style="background: #AE152D; color : #ffffff">
                <div class="h-100" data-simplebar>

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">

                        <div class="logo-box">
                            <a href="index.html" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('nursinglogo.webp') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('nursinglogo.webp') }}" alt="" height="24">
                                </span>
                            </a>
                            <a href="index.html" class="logo logo-dark">
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
                                <a href="{{route('parent.view.dashboard')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('parent.view.childrens')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Childrens</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{route('parent.view.profile')}}" class="text-white">
                                    <i data-feather="aperture"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                           



                          
                        </ul>

                    </div>
                    <!-- End Sidebar -->

                    <div class="clearfix"></div>

                </div>
            </div>
            <!-- Left Sidebar End -->