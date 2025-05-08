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

    // Fetch notifications based on user_id (Only the message and timestamp)
    $notificationQuery = "SELECT message, timestamp, status, remarks FROM tblnotifications ORDER BY timestamp DESC";
    $notificationStmt = $conn->prepare($notificationQuery);
    $notificationStmt->execute();
    $notificationResult = $notificationStmt->get_result();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Notifications</title>
        
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
        <!-- Add some basic styles -->
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>
            <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
            <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">

    </head>
    <body>
    <div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="container">
            <h1>Transaction History</h1>

            <!-- Table to display the notifications -->
            <table id="notificationsTable">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Remarks</th>
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

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($formattedTimestamp) . "</td>";
                            echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No transaction found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;"> 
        </div>
        </div>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>


    </body>
    </html>

    <script>
    $(document).ready(function () {
        $('#notificationsTable').DataTable();
    });
</script>
    

    <?php
    $content = ob_get_clean();
    include('templates/main.php');
    ?>
