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
                                        } else {
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
                                                echo "<a class='dropdown-item' href='#' onclick='openApprovalModal(" . $row['notificationId'] . ", " . $row['bookId'] . ", " . $row['borrowerId'] . ", \"" . $row['type'] . "\")'>" . htmlspecialchars($row['message']) . "</a>";
                                            }
                                        } else {
                                            echo "<a class='dropdown-item' href='#'>No notifications available</a>";
                                        }
                                        $stmt->close();
                                    }
                                ?>
                            </div>


                        
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
                        <span>University of Science and Technology of Southern Phillippines - Balubal Library System &copy; 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>



<div class="modal fade" id="inputDataModal" tabindex="-1" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="inputDataModalLabel">Please Complete Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="inputDataForm">
            <input type="hidden" id="notificationId" name="notificationId">
            <input type="hidden" id="bookId" name="bookId">
            <div class="form-group">
                <label for="borrowerId">ID Number</label>
                <input type="text" class="form-control" id="borrowerId" name="borrowerId" required readonly>
            </div>
            <div class="form-group">
                <label for="borrower">Borrower's Name</label>
                <input type="text" class="form-control" id="borrower" name="borrower" required readonly>
            </div>
            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" required readonly>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" required readonly>
            </div>
            <div class="form-group">
                <label for="librarianName">Librarian Name</label>
                <input type="text" class="form-control" id="librarianName" name="librarianName" required>
            </div>
            <div class="form-group">
                <label for="formattedReturnDates">Return Date:</label><br>
                <input type="text" id="formattedReturnDates" readonly class="form-group"><br>
                <input type="date" id="returnDates" name="returnDates" readonly required class="form-group" style="display: none;"><br>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <button class="btn btn-danger" data-dismiss="modal">Decline</button>

            </form>
        </div>
        </div>
    </div>
</div>


<!-- Modal for Approving the Notification -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to decline request?</p>
                <button class="btn btn-primary" id="approveBtn">Yes</button>
                <button class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Damage Report -->
<!-- Modal for Damage Report -->
<div class="modal fade" id="damageReportModal" tabindex="-1" aria-labelledby="damageReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="damageReportModalLabel">Penalty Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Is there any damage to the book?</p>
                <select class="form-control" id="damageSeverity" name="damageSeverity">
                    <option value="normal">Normal</option>
                    <option value="medium">Medium</option>
                    <option value="severe">Severe</option>
                </select>
                <div class="form-group mt-3">
                    <label for="damageCost">Penalty Cost</label>
                    <input type="number" class="form-control" id="damageCost" name="damageCost" placeholder="Enter cost" min="0" value="0">
                </div>

                <!-- Expected Return Date -->
                <p id="expectedReturnDate"></p>

                <!-- Time Penalty -->
                <p id="timePenalty"></p>

                <!-- Total Penalty -->
                <p id="totalPenalty"></p>

                <button class="btn btn-secondary" id="reportDamageBtn">Report Penalty</button>
                <button class="btn btn-primary" id="noDamageButton" data-dismiss="modal">No Penalty</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal for Inputting Data After Approval (for Borrowing/Returning) -->
<div class="modal fade" id="inputReturnModal" tabindex="-1" aria-labelledby="inputReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputReturnModalLabel">Return Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="inputReturnForm">
                    <input type="hidden" id="notificationId" name="notificationId">
                    <input type="hidden" id="bookId" name="bookId">
                    <div class="form-group">
                        <label for="returnerId">ID Number</label>
                        <input type="text" class="form-control" id="returnerId" name="returnerId" readonly>
                    </div>
                    <div class="form-group">
                        <label for="borrower1">Borrower's Name</label>
                        <input type="text" class="form-control" id="borrower1" name="borrower1" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="title1">Book Title</label>
                        <input type="text" class="form-control" id="title1" name="title1" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="author1">Author</label>
                        <input type="text" class="form-control" id="author1" name="author1" required readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Return Book</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentNotificationId = null;
    let currentBookId = null;
    let currentBorrowerId = null;
    let currentBook = null;
    let currentAuthor = null;
    let currentBorrower = null;
    let currentType = null;

    function openApprovalModal(notificationId, bookId, borrowerId, type, title, author, borrower) {
        currentNotificationId = notificationId;
        currentBookId = bookId;
        currentBorrowerId = borrowerId;
        currentType = type;
        currentBook = title;
        currentAuthor = author;
        currentBorrower = borrower;
        // Show the approve modal
        if (currentType === 'borrow') {
                // Show the input modal for borrowing
                $('#inputDataModal').modal('show');
                $('#notificationId').val(currentNotificationId);
                $('#bookId').val(currentBookId);
                $('#borrowerId').val(currentBorrowerId);
                $('#title').val(currentBook);
                $('#author').val(currentAuthor);
                $('#borrower').val(currentBorrower);
    
                // Dynamically calculate the return date
                const today = new Date();
    
                // Fetch borrower type via AJAX based on the current borrowerId
                $.ajax({
                    url: 'transactions/check-borrower-type.php',  // PHP script to fetch borrower type
                    type: 'POST',
                    data: { borrowerId: currentBorrowerId },
                    success: function (response) {
                        const borrowerType = response.trim(); // Get the borrower type and trim any extra spaces
    
                        // Calculate the return date based on borrower type
                        let returnDate = new Date(today);
    
                        if (borrowerType === 'Student') {
                            returnDate.setDate(today.getDate() + 3); // 3 days for students
                        } else {
                            returnDate.setDate(today.getDate() + 80); // 80 days for others
                        }
    
                        // Format the return date as YYYY-MM-DD for database and MM/DD/YYYY for display
                        const dbDay = String(returnDate.getDate()).padStart(2, '0');
                        const dbMonth = String(returnDate.getMonth() + 1).padStart(2, '0'); // Month is 0-based
                        const dbYear = returnDate.getFullYear();
                        const dbFormattedDate = `${dbYear}-${dbMonth}-${dbDay}`; // For database
                        const formattedDisplayDate = `${dbMonth}/${dbDay}/${dbYear}`; // For user display
    
                        // Set the values in the modal fields
                        $('#formattedReturnDates').val(formattedDisplayDate);  // Set user-friendly formatted date
                        $('#returnDates').val(dbFormattedDate);  // Set the database-friendly date
                    },
                    error: function () {
                        alert('Error fetching borrower type. Please try again.');
                    }
                });
            } else if (currentType === 'return') {
                $('#damageReportModal').modal('show');
            
                // Fetch return date and calculate penalty before the modal opens
                $.ajax({
                    url: 'process/fetch-return-date.php',  // Fetch return date and calculate penalty
                    type: 'POST',
                    data: {
                        borrowerId: currentBorrowerId,
                        bookId: currentBookId
                    },
                    success: function (data) {
                        const responseData = JSON.parse(data);
                        const returnDate = responseData.returnDate;
                        const penalty = responseData.penalty; // Assuming the penalty calculation logic is in the PHP file 
                
                        // Display the expected return date in the modal
                        $('#expectedReturnDate').text("Expected Return Date: " + returnDate);
                
                        const currentDate = new Date(); // Get current date and time
                        const returnDateObj = new Date(returnDate); // Convert returnDate string to Date object
                        returnDateObj.setHours(0, 0, 0, 0); // Set time to 12:00 AM

                        // If the return date is today, start penalty from 12:01 AM on the next day
                        if (currentDate.toISOString().split('T')[0] === returnDateObj.toISOString().split('T')[0]) {
                            returnDateObj.setDate(returnDateObj.getDate() + 1); // Set returnDateObj to the next day
                            returnDateObj.setHours(0, 1, 0, 0); // Set time to 12:01 AM of the next day
                        }

                        // Calculate the difference in hours between the expected return date and the current date
                        const timeDiff = currentDate - returnDateObj; // Difference in milliseconds
                        const hoursDiff = Math.floor(timeDiff / (1000 * 60 * 60))*5; // Convert milliseconds to hours
                
                        // If the difference is positive, we have a penalty, otherwise, no penalty
                        const timePenalty = hoursDiff > 0 ? hoursDiff : 0;
                
                        // Display the current date and the calculated time penalty
                        $('#currentDate').text("Current Date: " + currentDate.toISOString().split('T')[0]);
                        $('#timePenalty').text("Time Penalty: " + (timePenalty > 0 ? "Php " + (timePenalty * penalty) : "No penalty"));
                
                        // Initialize Total Penalty to just the time penalty
                        let totalPenalty = timePenalty * penalty; // Assuming the penalty is applied per hour of delay
                
                        // Initialize the damage cost to 0
                        $('#damageCost').val(0);  // Default to 0 as damage cost
                        $('#totalPenalty').text("Total Penalty: Php " + totalPenalty.toFixed(2));
                
                        // Update Total Penalty when damage cost changes
                        $('#damageCost').on('input', function() {
                            const damageCost = parseFloat($('#damageCost').val()) || 0;
                            totalPenalty = (timePenalty * penalty) + damageCost; // Add damage cost to time penalty
                            $('#totalPenalty').text("Total Penalty: Php " + totalPenalty.toFixed(2)); // Update the total penalty display
                        });
            
                        // Handle damage report submission
                        $('#reportDamageBtn').on('click', function () {
                            const damageSeverity = $('#damageSeverity').val();
                            const damageCost = $('#damageCost').val(); // The value entered by the user for the damage cost
                            const finalTotalPenalty = totalPenalty; // The final total penalty, which includes time penalty and damage cost
            
                            // Send the final total penalty as damageCost to the backend
                            $.ajax({
                                url: 'process/report-damage.php',
                                type: 'POST',
                                data: {
                                    borrowerId: currentBorrowerId,
                                    bookId: currentBookId,
                                    damageSeverity: damageSeverity,
                                    damageCost: finalTotalPenalty // Send the final total penalty
                                },
                                success: function (response) {
                                    alert('Penalty reported successfully.');
                                    $('#damageReportModal').modal('hide'); // Close the damage report modal
                                    $('#inputReturnModal').modal('show');
                                    $('#notificationId').val(currentNotificationId);
                                    $('#bookId').val(currentBookId);
                                    $('#title1').val(currentBook);
                                    $('#author1').val(currentAuthor);
                                    $('#borrower1').val(currentBorrower);
                                    $('#returnerId').val(currentBorrowerId);
                                },
                                error: function () {
                                    alert('Error reporting damage. Please try again.');
                                }
                            });
                        });
                    },
                    error: function () {
                        alert('Error fetching return date and penalty. Please try again.');
                    }
                });
                    

            
                $('#noDamageButton').on('click', function() {
                    // Just show the return modal when "No Damage" is clicked
                    $('#damageReportModal').modal('hide');  // Hide the damage report modal
                    $('#inputReturnModal').modal('show');  // Show the return modal
            
                    // Set values for the return form
                    $('#notificationId').val(currentNotificationId);
                    $('#bookId').val(currentBookId);
                    $('#title1').val(currentBook);
                    $('#author1').val(currentAuthor);
                    $('#borrower1').val(currentBorrower);
                    $('#returnerId').val(currentBorrowerId);
                });
            }

        
    }
    

    $(document).ready(function () {

        // Submit the input form for borrowing
        $('#inputDataForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission behavior
            
            const bookId = $('#bookId').val();
            const notificationId = $('#notificationId').val();
            const borrowerId = $('#borrowerId').val();
            const librarianName = $('#librarianName').val();
            let returnDate = $('#returnDates').val(); // This will be set dynamically
    
            // Now submit the form as usual
            $.ajax({
                url: 'transactions/borrow-book.php',
                type: 'POST',
                data: { bookId, notificationId, idNumber: borrowerId, librarianName, returnDate: returnDate },
                success: function (response) {
                    alert(response); // Display success message
                    $('#inputDataModal').modal('hide'); // Close the input modal
                    location.reload(); // Reload the page to reflect changes
                },
                error: function () {
                    alert('Error borrowing the book. Please try again.');
                }
            });
        });
    });


        // Submit the return form
        $('#inputReturnForm').on('submit', function (e) {
            e.preventDefault();

            const notificationId = $('#notificationId').val();
            const bookId = $('#bookId').val();
            const returnerId = $('#returnerId').val();

            $.ajax({
                url: 'transactions/return-book.php',
                type: 'POST',
                data: {notificationId, bookId, idNumber: returnerId },
                success: function (response) {
                    alert(response);
                    $('#inputReturnModal').modal('hide');
                    location.reload();
                },
                error: function () {
                    alert('Error returning the book. Please try again.');
                }
            });
        });

    
    document.addEventListener("DOMContentLoaded", function () {
        // When the Decline button in inputDataModal is clicked
    document.querySelector("#inputDataModal .btn-danger").addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default behavior
            $("#inputDataModal").modal("hide"); // Hide inputDataModal
            $("#approveModal").modal("show"); // Show approveModal
        });

        // When the No button in approveModal is clicked
    document.querySelector("#approveModal .btn-danger").addEventListener("click", function () {
            $("#approveModal").modal("hide"); // Hide approveModal
            $("#inputDataModal").modal("show"); // Show inputDataModal
        });

        // When the Yes button in approveModal is clicked
        document.querySelector("#approveBtn").addEventListener("click", function () {
                $("#approveModal").modal("hide"); // Hide approveModal
                $("#inputDataModal").modal("hide"); // Hide inputDataModal
            });
        });

        // When the Yes button in approveModal is clicked (for Decline)
        document.querySelector("#approveBtn").addEventListener("click", function () {
            // Send the "Decline" update to the backend
            $.ajax({
                url: 'process/decline-notification.php', // PHP script to decline the notification
                type: 'POST',
                data: { notificationId: currentNotificationId },
                success: function (response) {
                    // Handle response from the server (e.g., success or error message)
                    alert(response);
                    
                    // Close the modals
                    $("#approveModal").modal("hide");
                    $("#inputDataModal").modal("hide");

                    // Optionally, reload the page or fetch the notifications again
                    location.reload();
                },
                error: function () {
                    alert('Error declining the notification. Please try again.');
                }
            });
        });






    function markNotificationsAsRead() {
        $.ajax({
            url: 'process/mark-notifications.php',
            type: 'POST',
            success: function(response) {
                try {
                    const data = JSON.parse(response);  // Parse the response if it's JSON
                    if (data.success) {
                        // Hide the unread count badge and reset the count to 0
                        $('#unreadCount').text('0');  // Reset the count to 0 and hide the badge
                    } else {
                        alert(data.message || 'Error marking notifications as read.');
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    alert('Error processing notifications.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error marking notifications as read.');
            }
        });
    }


        document.getElementById('changePasswordLink').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the link from redirecting
            alert("Please contact the Administrator to change your password.");
        });

        document.addEventListener('DOMContentLoaded', function () {
            const formattedReturnDatesInput = document.getElementById('formattedReturnDates');
            const returnDatesInput = document.getElementById('returnDates');
        
            // Set the return date (e.g., 3 days later)
            const today = new Date();
            const returnDates = new Date(today);
            returnDates.setDate(today.getDate() + 3);
        
            // Format the return date as YYYY-MM-DD for the hidden input
            const dbYear = returnDates.getFullYear();
            const dbMonth = String(returnDates.getMonth() + 1).padStart(2, '0'); // Month is 0-based
            const dbDay = String(returnDates.getDate()).padStart(2, '0');
            const dbFormattedDate = `${dbYear}-${dbMonth}-${dbDay}`;
        
            // Set the value for the hidden input (database format)
            returnDatesInput.value = dbFormattedDate;
        
            // Format the return date as MM/DD/YYYY for user display
            const userMonth = String(returnDates.getMonth() + 1).padStart(2, '0');
            const userDay = String(returnDates.getDate()).padStart(2, '0');
            const userFormattedDate = `${userMonth}/${userDay}/${dbYear}`;
        
            // Set the value for the visible input (user-friendly format)
            formattedReturnDatesInput.value = userFormattedDate;
        });
        
        
        function fetchNotifications() {
            $.ajax({
                url: 'process/fetch-notifications.php',
                type: 'GET',
                dataType: 'json', // Expect JSON data
                success: function(response) {
                    if (response.notifications && Array.isArray(response.notifications)) {
                        const notifications = response.notifications;

                        // Update the notification dropdown
                        const dropdownMenu = $('.dropdown-menu[aria-labelledby="notifDropdown"]');
                        dropdownMenu.empty(); // Clear existing notifications

                        if (notifications.length > 0) {
                            notifications.forEach(notification => {
                                dropdownMenu.append(`
                                    <a class='dropdown-item d-flex align-items-center' href='#' 
                                    onclick='openApprovalModal(${notification.notificationId}, ${notification.bookId}, ${notification.borrowerId}, "${notification.type}", "${notification.bookTitle}", "${notification.authorFullName}", "${notification.borrowerFullName}")'>
                                        <div class='notification-icon mr-2'>
                                            <i class='fas ${notification.type === "approval" ? "fa-check-circle text-success" : "fa-info-circle text-primary"}'></i>
                                        </div>
                                        <div class='notification-text'>
                                            <span class='font-weight-bold'>${notification.message}</span>
                                            <small class='d-block text-muted'>Borrower ID: ${notification.borrowerId} | ${notification.timestamp}</small>
                                        </div>
                                    </a>
                                `);
                            });
                        } else {
                            dropdownMenu.append(`
                                <a class='dropdown-item text-center text-muted' href='#'>
                                    <i class='fas fa-bell-slash'></i> No notifications available
                                </a>
                            `);
                        }

                        // Update the unread count
                        const unreadCount = notifications.filter(n => n.status === 'unread').length;
                        const unreadCountBadge = $('#unreadCount');

                        if (unreadCount > 0) {
                            unreadCountBadge.text(unreadCount).show();
                            $('#notifBell').addClass('unread'); // Add red color to the bell
                        } else {
                            unreadCountBadge.text('0').hide();
                            $('#notifBell').removeClass('unread'); // Default bell color
                        }
                    } else {
                        console.error('Unexpected response format:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching notifications:', status, error);
                }
            });
        }

        // Poll for new notifications every 10 seconds
        setInterval(fetchNotifications, 5000);

        // Fetch notifications when the page loads
        document.addEventListener('DOMContentLoaded', fetchNotifications);
</script>
