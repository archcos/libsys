<?php
session_start();
include('../process/db-connect.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];

    if (empty($userId)) {
        echo 'User ID is required.';
        exit();
    }

    try {
        // Prepare the query to delete the user
        $stmt = $conn->prepare("DELETE FROM tbluser WHERE userId = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        echo 'User deleted successfully';
        $stmt->close();
    } catch (Exception $e) {
        // Handle errors
        error_log('Error in delete-user.php: ' . $e->getMessage());
        echo 'An error occurred. Please try again.';
    }
}
?>
