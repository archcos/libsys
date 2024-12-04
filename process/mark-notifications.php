<?php
    session_start();
    include('db-connect.php');  // Database connection

    // Mark all unread notifications as read
    $sql = "UPDATE tblnotifications SET status = 'read' WHERE status = 'unread'";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->execute();
        $stmt->close();
    }

    // Return the updated unread count (should be 0 after marking as read)
    $sql = "SELECT COUNT(*) AS unread_count FROM tblnotifications WHERE status = 'unread'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $unreadCount = $row['unread_count'];

    // Respond with the new unread count
    echo json_encode(['success' => true, 'unreadCount' => $unreadCount]);
?>
