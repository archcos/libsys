<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['authorId'])) {
        echo "Author ID is required.";
        exit;
    }

    $authorId = intval($_POST['authorId']);

    if ($authorId <= 0) {
        echo "Invalid author ID.";
        exit;
    }

    try {
        // Check if the author is associated with any books in tblbooks
        $checkQuery = $conn->prepare("SELECT COUNT(*) AS count FROM tblbooks WHERE authorId = ?");
        $checkQuery->bind_param("i", $authorId);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();
        $row = $checkResult->fetch_assoc();
        $checkQuery->close();

        if ($row['count'] > 0) {
            echo "Cannot delete author. This author is associated with one or more books.";
        } else {
            // Proceed to delete the author if not associated with any books
            $deleteQuery = $conn->prepare("DELETE FROM tblauthor WHERE authorId = ?");
            $deleteQuery->bind_param("i", $authorId);
            if ($deleteQuery->execute()) {
                echo "Author deleted successfully!";
            } else {
                echo "Failed to delete author.";
            }
            $deleteQuery->close();
        }
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
