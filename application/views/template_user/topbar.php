        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item d-flex align-items-center">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $user['nama']; ?></span>
                            <img class="img-profile rounded-circle"
                                src="<?php echo base_url('assets/img/profile/') . $user['image']; ?>"
                                style="width: 40px; height: 40px; margin-left: 10px;">
                            <a class="btn btn-danger btn-sm ml-3"
                                href="<?php echo base_url('auth/logout'); ?>"
                                onclick="return confirm('Apakah Anda yakin ingin logout?')">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-1"></i> Logout
                            </a>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->