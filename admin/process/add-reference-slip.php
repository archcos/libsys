<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

// Include database connection
include('db-connect.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $borrowerId = $_POST['borrowerId'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $callNumber = $_POST['callNumber'];
    $subLocation = $_POST['subLocation'];
    $date = $_POST['date'];

    try {
        $stmt = $conn->prepare("INSERT INTO tblreference (borrowerId, type, title, author, category, callNumber, subLocation, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $borrowerId, $type, $title, $author, $category, $callNumber, $subLocation, $date);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode('Reference slip added successfully.');
        } else {
            echo json_encode('Failed to add reference slip.');
        }
    } catch (mysqli_sql_exception $e) {
        // Handle foreign key constraint violation (error code 1452)
        if ($e->getCode() == 1452) {
            echo json_encode('The borrower ID provided does not exist. Please select a valid borrower.');
        } else {
            echo json_encode('An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
?>
