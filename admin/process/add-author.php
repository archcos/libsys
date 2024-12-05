<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);

    if (!empty($firstName) && !empty($lastName)) {
        $query = $conn->prepare("INSERT INTO tblauthor (firstName, lastName) VALUES (?, ?)");
        $query->bind_param("ss", $firstName, $lastName);

        if ($query->execute()) {
            echo "Author added successfully!";
        } else {
            echo "Failed to add author: " . $conn->error;
        }
    } else {
        echo "First name and last name cannot be empty!";
    }
} else {
    echo "Invalid request method!";
}
?>
