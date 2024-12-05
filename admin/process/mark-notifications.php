<?php
session_start();
include('db-connect.php');

// Update notification status for all users
$sql = "UPDATE tblnotifications SET status = 'read' WHERE status = 'unread'";

if ($stmt = $conn->prepare($sql)) {
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        // Log error for debugging
        error_log('Error executing query: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    // Log error for debugging if SQL preparation fails
    error_log('Error preparing the statement: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare query']);
}
?>
