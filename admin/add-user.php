<?php
session_start();
include('../process/db-connect.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = sha1($_POST['password']);  // Encrypt the password using SHA1
    $accountType = $_POST['accountType'];  // Get the account type

    // Validate input
    if (empty($username) || empty($firstName) || empty($lastName) || empty($accountType) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    try {
        // Check if the username already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM tbluser WHERE username = ?");
        $checkStmt->bind_param('s', $username);
        $checkStmt->execute();
        $checkStmt->bind_result($usernameCount);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($usernameCount > 0) {
            // Username already exists, send a warning message
            echo json_encode(['success' => false, 'message' => 'Username already exists. Please choose a different one.']);
            exit();
        }

        // Prepare the query to insert the new user
        $stmt = $conn->prepare("INSERT INTO tbluser (username, firstName, lastName, accountType, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $username, $firstName, $lastName, $accountType, $password);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'User added successfully']);
        $stmt->close();
    } catch (Exception $e) {
        // Handle errors
        error_log('Error in add-user.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
}
?>
