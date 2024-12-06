<?php
include('db-connect.php');

// Fetch notifications ordered by latest
$sql = "SELECT notificationId, borrowerId, bookId, message, status, type, timestamp FROM tblnotifications ORDER BY notificationId DESC";
$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format the timestamp
        $dateTime = new DateTime($row['timestamp']);
        $formattedTimestamp = $dateTime->format('M j, Y g:i A'); // e.g., "Wed, Dec 6, 2023 2:30 PM"

        $notifications[] = [
            'notificationId' => $row['notificationId'],
            'borrowerId' => $row['borrowerId'],
            'bookId' => $row['bookId'],
            'message' => htmlspecialchars($row['message']),
            'status' => $row['status'],
            'type' => $row['type'],
            'timestamp' => $formattedTimestamp // Use formatted timestamp
        ];
    }
}

// Return notifications as JSON
echo json_encode(['notifications' => $notifications]);
$conn->close();
?>
