<?php
// Fetch notifications
include('db-connect.php');

// Example SQL to get notifications
// Fetch all unread notifications from the database
$sql = "SELECT * FROM tblnotifications WHERE status = 'unread' ORDER BY notificationId DESC";
$result = $conn->query($sql);

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row; // Add each unread notification to the array
}

// Send the unread notifications as a JSON response
header('Content-Type: application/json');
echo json_encode($notifications);

?>
