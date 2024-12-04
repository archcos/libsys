<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BaLibSys</title>

    <!-- Custom fonts for this template-->
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<style>
    #notifBell {
    color: #d1d3e2; /* Default gray */
    }
    #notifBell.unread {
        color: red; /* Turn red when unread */
    }

</style>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">


            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Include Sidebar PHP content here -->
            <?php include('templates/sidebar.php'); ?>

        </ul>
        <!-- End of Sidebar -->

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
                        <li class="nav-item dropdown no-arrow">
                            <!-- Notification Bell Icon -->
                            <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="markNotificationsAsRead()">
                                <i class="fas fa-bell" id="notifBell" style="font-size: 1.5rem;"></i>
                                <?php
                                    // Check for unread notifications
                                    include('process/db-connect.php');
                                    $sql = "SELECT COUNT(*) AS unread_count FROM tblnotifications WHERE status = 'unread'";
                                    if ($stmt = $conn->prepare($sql)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_assoc();
                                        $unreadCount = $row['unread_count'];

                                        // Display the unread count as a red badge
                                        if ($unreadCount > 0) {
                                            echo "<span class='badge badge-danger' id='unreadCount' style='position: absolute; top: 0; right: 0;'>$unreadCount</span>";
                                        }
                                        $stmt->close();
                                    }
                                ?>
                            </a>

                            <!-- Dropdown Menu with Scrollable Notifications -->
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notifDropdown" style="max-height: 300px; overflow-y: auto;">
                                <?php
                                    // Fetch all notifications, ordered by timestamp
                                    $sql = "SELECT * FROM tblnotifications ORDER BY notificationId DESC";
                                    if ($stmt = $conn->prepare($sql)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Check if there are notifications
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<a class='dropdown-item' href='#'>" . htmlspecialchars($row['message']) . "</a>";
                                            }
                                        } else {
                                            echo "<a class='dropdown-item' href='#'>No notifications available</a>";
                                        }
                                        $stmt->close();
                                    }
                                ?>
                            </div>
                        </li>

                        
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                           
                            <!-- User Dropdown -->
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php 
                                        echo isset($_SESSION['firstName']) && isset($_SESSION['lastName']) 
                                            ? $_SESSION['firstName'] . ' ' . $_SESSION['lastName'] 
                                            : 'Guest';
                                    ?>
                                </span>
                                <img class="img-profile rounded-circle" src="assets/img/profile.png">
                            </a>

                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" id="changePasswordLink">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Change Password
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="process/logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>

                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <?php echo $content; ?> <!-- Dynamic content area -->
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>University of Science and Technology of Southern Phillippines - Balubal Library System &copy; 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <!-- <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a> -->

    <!-- Bootstrap core JavaScript-->
    <!-- <script src="assets/vendor/jquery/jquery.min.js"></script> -->

    <!-- Core plugin JavaScript-->
    <!-- <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script> -->

    <!-- Custom scripts for all pages-->
     
    <script src="assets/js/sb-admin-2.min.js"></script>

</body>

</html>
<script>
    function markNotificationsAsRead() {
        // Make an AJAX request to mark notifications as read
        fetch('process/mark-notifications.php')
            .then(response => response.json())
            .then(data => {
                // Hide the unread count if no unread notifications exist
                if (data.success && data.unreadCount === 0) {
                    document.getElementById('unreadCount').style.display = 'none';
                } else {
                    document.getElementById('unreadCount').textContent = data.unreadCount;
                }
            })
            .catch(error => console.error('Error:', error));
    }

    document.getElementById('changePasswordLink').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the link from redirecting
        alert("Please contact the Administrator to change your password.");
    });
</script>
