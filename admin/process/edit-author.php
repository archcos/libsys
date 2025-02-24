<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['authorId']) || !isset($_POST['firstName']) || !isset($_POST['lastName'])) {
        echo "All fields are required.";
        exit;
    }

    $authorId = intval($_POST['authorId']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);

    if ($authorId <= 0 || empty($firstName) || empty($lastName)) {
        echo "Invalid author data.";
        exit;
    }

    try {
        $updateQuery = $conn->prepare("UPDATE tblauthor SET firstName = ?, lastName = ? WHERE authorId = ?");
        $updateQuery->bind_param("ssi", $firstName, $lastName, $authorId);

        if ($updateQuery->execute()) {
            echo "Author updated successfully!";
        } else {
            echo "Failed to update author.";
        }

        $updateQuery->close();
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
