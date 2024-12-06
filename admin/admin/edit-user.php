<?php
session_start();
include('../process/db-connect.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST
    $userId = $_POST['userId'];
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $accountType = $_POST['accountType'];
    $password = $_POST['password']; // Empty if no change in password

    // Validate input
    if (empty($userId) || empty($username) || empty($firstName) || empty($lastName) || empty($accountType)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // If password is not empty, hash it
    if (!empty($password)) {
        $password = sha1($password); // You can change this to a better hashing method
        $passwordQuery = ", password = '$password'";
    } else {
        $passwordQuery = "";
    }

    try {
        // Update query
        $stmt = $conn->prepare("UPDATE tbluser SET username = ?, firstName = ?, lastName = ?, accountType = ? $passwordQuery WHERE userId = ?");
        if (!empty($password)) {
            $stmt->bind_param('sssss', $username, $firstName, $lastName, $accountType, $userId);
        } else {
            $stmt->bind_param('ssss', $username, $firstName, $lastName, $accountType);
        }

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode('User updated successfully.');
        } else {
            echo json_encode('No changes made or user not found.');
        }
        
        $stmt->close();
    } catch (Exception $e) {
        // Handle errors
        error_log('Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
}
?>
