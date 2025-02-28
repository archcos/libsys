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
    $referenceId = $_POST['referenceId'];
    $borrowerId = $_POST['borrowerId'];
    $type = $_POST['type'];
    $author = $_POST['author'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $callNumber = $_POST['callNumber'];
    $subLocation = $_POST['subLocation'];
    $date = $_POST['date'];

    try {
        $stmt = $conn->prepare("UPDATE tblreference SET borrowerId = ?, type = ?, author = ?, title = ?, category = ?, callNumber = ?, subLocation = ?, date = ? WHERE referenceId = ?");
        $stmt->bind_param("isssssssi", $borrowerId, $type, $author, $title, $category, $callNumber, $subLocation, $date, $referenceId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode('Reference slip updated successfully.');
        } else {
            echo json_encode('No changes were made.');
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode('Error updating reference slip. Invalid Input or Borrower ID.');
    }
}
?>
