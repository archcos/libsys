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
        // Prepare the query to fetch the user data
        $stmt = $conn->prepare("SELECT * FROM tbluser WHERE userId = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        // Handle errors
        error_log('Error in get-user.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
}
?>
