<?php
include('db-connect.php');

// Fetch notifications ordered by latest
$sql = "SELECT notificationId, borrowerId, bookId, message, status, type FROM tblnotifications ORDER BY notificationId DESC";
$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'notificationId' => $row['notificationId'],
            'borrowerId' => $row['borrowerId'],
            'bookId' => $row['bookId'],
            'message' => htmlspecialchars($row['message']),
            'status' => $row['status'],
            'type' => $row['type']
        ];
    }
}

echo json_encode(['notifications' => $notifications]);
$conn->close();
?>
