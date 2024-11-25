<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>USTP Balubal Library System</title>
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
    <link href="assets/plugins/simplebar/simplebar.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/images/favicon.png" rel="shortcut icon" />
</head>

<body id="page-top" class="navbar-fixed sidebar-fixed">
    <script>
        NProgress.configure({ showSpinner: false });
        NProgress.start();
    </script>

    <div id="toaster"></div>
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="assets/images/favicon.png" alt="Logo" width="38" height="38">
                </div>
                <div class="sidebar-brand-text mx-3">USTP Library</div>
            </a>
            <hr class="sidebar-divider my-0">
            <?= $this->include('template/sidebar') ?>
            <hr class="sidebar-divider">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebar-toggler" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <span class="page-title">Library Dashboard</span>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="images/user/user-xs-01.jpg" class="img-profile rounded-circle" alt="User  Image" />
                                <span class="d-none d-lg-inline-block">John Doe</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="user-profile.html">
                                    <i class="mdi mdi-account-outline"></i> My Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="sign-in.html">
                                    <i class="mdi mdi-logout"></i> Log Out
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?= $this->renderSection('content'); ?>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; <span id="copy-year"></span> USTP Library System</span>
                    </div>
                </div>
                <script>
                    var d = new Date();
                    var year = d.getFullYear();
                    document.getElementById("copy-year").innerHTML = year;
                </script>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
</body>
</html>