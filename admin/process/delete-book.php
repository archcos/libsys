<?php
// Include database connection
if (isset($_POST['bookId']) && !empty($_POST['bookId'])) {
    $bookId = $_POST['bookId'];

    // Check if the book is currently borrowed (returned = 'No')
    $checkQuery = "SELECT 1 FROM tblreturnborrow WHERE bookId = ? AND returned = 'No'";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If there are records with returned = 'No', deny deletion
        echo "This book is currently borrowed and cannot be deleted.";
    } else {
        // Proceed to delete the book
        $deleteQuery = "DELETE FROM tblbooks WHERE bookId = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $bookId);

        if ($deleteStmt->execute()) {
            echo "Book deleted successfully!";
        } else {
            echo "Error deleting book. Please try again.";
        }

        $deleteStmt->close();
    }
    $stmt->close();
} else {
    echo "No book ID provided.";
}
$conn->close();


?>
