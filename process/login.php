<?php
session_start(); // Start the session
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1);    // Enable error logging
ini_set('error_log', __DIR__ . '/php-error.log'); // Log errors to a file

// Include the database connection
require 'db-connect.php'; // Adjust the path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (empty($input['username']) || empty($input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
        exit();
    }

    $username = $input['username'];
    $password = $input['password'];

    try {
        // Query the database using mysqli
        $stmt = $conn->prepare('SELECT * FROM tbluser WHERE username = ?');
        $stmt->bind_param('s', $username); // 's' means the parameter is a string
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Verify the password using SHA1
            if (sha1($password) === $user['password']) {
                $updateStmt = $conn->prepare('UPDATE tbluser SET lastLogin = NOW() WHERE userId = ?');
                $updateStmt->bind_param('i', $user['userId']);
                $updateStmt->execute();

                $_SESSION['user_id'] = $user['userId'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['lastName'] = $user['lastName'];
                $_SESSION['accountType'] = $user['accountType']; // Add the account type

                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }


        $stmt->close();
    } catch (Exception $e) {
        // Log the error and send a generic response
        error_log('Error in sign-in.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An internal error occurred.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>


