<?php
session_start(); // Start the session
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1);    // Enable error logging
ini_set('error_log', __DIR__ . '/php-error.log'); // Log errors to a file

// Include the database connection
require '../db/db-connect.php'; // Adjust the path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'idNumber' exists in the POST data
    if (empty($_POST['idNumber'])) {
        echo json_encode(['success' => false, 'message' => 'ID number is required.']);
        exit();
    }

    $idNumber = $_POST['idNumber'];  // Retrieve the ID number directly from POST data

    try {
        // Query the database using mysqli
        $stmt = $conn->prepare('SELECT * FROM tblborrowers WHERE idNumber = ?');
        $stmt->bind_param('s', $idNumber); // 's' means the parameter is a string (ID number)
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['user_id'] = $user['idNumber'];  // Store ID number in session (or user ID if needed)
            $_SESSION['username'] = $user['firstName'] . ' ' . $user['surName'];  // Assuming you're storing full name
            $_SESSION['borrowerType'] = $user['borrowerType'];  // Store borrower type if necessary

            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid ID number']);
        }

        $stmt->close();
    } catch (Exception $e) {
        // Log the error and send a generic response
        error_log('Error in login.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An internal error occurred.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>