<?php
session_start();
include('../process/db-connect.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];

    if (empty($userId)) {
        echo json_encode(['success' => false, 'message' => 'User ID is required.']);
        exit();
    }

    try {
        // Prepare the query to delete the user
        $stmt = $conn->prepare("DELETE FROM tbluser WHERE userId = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        $stmt->close();
    } catch (Exception $e) {
        // Handle errors
        error_log('Error in delete-user.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
}
?>
