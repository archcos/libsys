<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authorId = intval($_POST['authorId']);

    if ($authorId > 0) {
        $query = $conn->prepare("DELETE FROM tblauthor WHERE authorId = ?");
        $query->bind_param("i", $authorId);

        if ($query->execute()) {
            echo "Author deleted successfully!";
        } else {
            echo "Failed to delete author: " . $conn->error;
        }
    } else {
        echo "Invalid author ID!";
    }
} else {
    echo "Invalid request method!";
}
?>
