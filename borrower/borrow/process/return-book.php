<?php
session_start();
include('../db/db-connect.php');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['bookId'];
    $userId = $_POST['userId'];
    $username = $_POST['username'];

    // Validate bookId and userId
    if (!empty($bookId) && !empty($userId)) {
        // Fetch book title for the notification
        $bookQuery = "SELECT title FROM tblbooks WHERE bookId = ?";
        if ($stmt = $conn->prepare($bookQuery)) {
            $stmt->bind_param("i", $bookId);
            $stmt->execute();
            $bookResult = $stmt->get_result();
            if ($bookResult->num_rows > 0) {
                $book = $bookResult->fetch_assoc();
                $bookTitle = $book['title'];

                // Create the notification message
                $message = "$username has returned the book: $bookTitle";

                // Insert notification into tblnotifications
                $notifQuery = "INSERT INTO tblnotifications (borrowerId, bookId, message, status, type) VALUES (?, ?, ?, 'unread', 'return')";
                if ($notifStmt = $conn->prepare($notifQuery)) {
                    $notifStmt->bind_param("iis", $userId, $bookId, $message);
                    if ($notifStmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Book return processed successfully and notification sent.';
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Error sending notification.';
                    }
                    $notifStmt->close();
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error preparing notification query.';
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Book not found.';
            }
            $stmt->close();
        } else {
            $response['success'] = false;
            $response['message'] = 'Error preparing book query.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid data received.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
