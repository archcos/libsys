<?php
// Include database connection file
include('../process/db-connect.php');

// Check if the bookId is received
if (isset($_POST['bookId'])) {
    $bookId = intval($_POST['bookId']); // Sanitize input

    // Update the returned status in tblreturnborrow
    $updateReturn = "UPDATE tblreturnborrow SET returned = 'Yes' WHERE bookId = ?";
    $stmt1 = $conn->prepare($updateReturn);
    $stmt1->bind_param("i", $bookId);

    // Update the available status in tblbooks
    $updateAvailable = "UPDATE tblbooks SET available = 'Yes' WHERE bookId = ?";
    $stmt2 = $conn->prepare($updateAvailable);
    $stmt2->bind_param("i", $bookId);

    if ($stmt1->execute() && $stmt2->execute()) {
        echo "Book successfully marked as returned.";
    } else {
        echo "Error marking book as returned.";
    }

    // Close statements
    $stmt1->close();
    $stmt2->close();
} else {
    echo "Invalid request. Book ID is missing.";
}

// Close the database connection
$conn->close();
?>
