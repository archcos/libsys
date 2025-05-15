<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: sign-in.php'); // Redirect to login if not logged in
        exit;
    }
    
    include('process/db-connect.php'); // Database connection
    ob_start();    

    // Get logged-in user's ID
    $userId = $_SESSION['user_id']; 

    // Fetch notifications with borrower and book details
    $notificationQuery = "SELECT n.*, 
                         b.title as book_title,
                         br.firstName as borrower_fname, 
                         br.middleName as borrower_mname,
                         br.surName as borrower_lname,
                         br.idNumber as borrower_id
                         FROM tblnotifications n
                         LEFT JOIN tblbooks b ON n.bookId = b.bookId
                         LEFT JOIN tblborrowers br ON n.borrowerId = br.idNumber
                         ORDER BY n.timestamp DESC";
    $notificationStmt = $conn->prepare($notificationQuery);
    $notificationStmt->execute();
    $notificationResult = $notificationStmt->get_result();
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
    </div>

    <!-- Notifications Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="notificationsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>TIMESTAMP</th>
                            <th>MESSAGE</th>
                            <th>STATUS</th>
                            <th>REMARKS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are any notifications for the user
                        if ($notificationResult->num_rows > 0) {
                            // Loop through and display the notifications
                            while ($row = $notificationResult->fetch_assoc()) {
                                // Format the timestamp to AM/PM format
                                $timestamp = new DateTime($row['timestamp']);
                                $formattedTimestamp = $timestamp->format('g:i A, F j, Y'); // Format: 12-hour time with AM/PM

                                // Store notification details as data attributes
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($formattedTimestamp) . "</td>";
                                echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                                echo "<td>
                                        <button type='button' class='btn btn-info btn-sm' onclick='showNotificationDetails(\"" . 
                                            addslashes($formattedTimestamp) . "\", \"" . 
                                            addslashes($row['message']) . "\", \"" . 
                                            addslashes($row['status']) . "\", \"" . 
                                            addslashes($row['type']) . "\", \"" . 
                                            addslashes($row['remarks']) . "\", \"" . 
                                            addslashes($row['book_title']) . "\", \"" . 
                                            addslashes($row['borrower_fname'] . ' ' . $row['borrower_mname'] . ' ' . $row['borrower_lname']) . "\", \"" . 
                                            addslashes($row['borrower_id']) . "\")'>
                                            <i class='fas fa-eye'></i> View
                                        </button>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No notifications found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Date & Time:</strong> <span id="modal-timestamp"></span></p>
                        <p><strong>Message:</strong> <span id="modal-message"></span></p>
                        <p><strong>Type:</strong> <span id="modal-type"></span></p>
                        <p><strong>Status:</strong> <span id="modal-status"></span></p>
                        <p><strong>Remarks:</strong> <span id="modal-remarks"></span></p>
                        <hr>
                        <p><strong>Book:</strong> <span id="modal-book"></span></p>
                        <p><strong>Borrower:</strong> <span id="modal-borrower"></span></p>
                        <p><strong>Borrower ID:</strong> <span id="modal-borrower-id"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.notification-row {
    cursor: pointer;
}
.notification-row:hover {
    background-color: #f8f9fa;
}
</style>

<script>
$(document).ready(function() {
    $('#notificationsTable').DataTable({
        "order": [[0, "desc"]]
    });
});

function showNotificationDetails(timestamp, message, status, type, remarks, book, borrower, borrowerId) {
    $('#modal-timestamp').text(timestamp || 'N/A');
    $('#modal-message').text(message || 'N/A');
    $('#modal-type').text(type || 'N/A');
    $('#modal-status').text(status || 'N/A');
    $('#modal-remarks').text(remarks || 'N/A');
    $('#modal-book').text(book || 'N/A');
    $('#modal-borrower').text(borrower || 'N/A');
    $('#modal-borrower-id').text(borrowerId || 'N/A');
    
    $('#notificationModal').modal('show');
}
</script>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
