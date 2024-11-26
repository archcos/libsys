<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require 'db-connect.php'; // This should be the file where the $conn mysqli connection is defined

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if input data is provided
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
                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }

        // Close the statement
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
