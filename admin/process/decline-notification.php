<?php
// decline-notification.php

// Include your database connection
include('db-connect.php');

// Get the notificationId from POST request
$notificationId = isset($_POST['notificationId']) ? $_POST['notificationId'] : null;

if ($notificationId) {
    // Prepare the SQL query to update the remarks
    $sql = "UPDATE tblnotifications SET remarks = 'Declined' WHERE notificationId = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind the notificationId to the query
        $stmt->bind_param("i", $notificationId);
        
        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['Borrower request declined successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update notification']);
        }
        
        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'SQL preparation failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No notificationId provided']);
}

// Close the database connection
$conn->close();
?>
