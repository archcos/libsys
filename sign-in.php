<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require 'db-connect.php';

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
        // Query the database
        $stmt = $pdo->prepare('SELECT * FROM tbluser WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
