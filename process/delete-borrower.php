<?php
// Include your database connection
include('db-connect.php');

// Check if the necessary POST data is received
if (isset($_POST['borrowerId'])) {
    $borrowerId = $_POST['borrowerId'];

    // Delete the borrower
    $query = "DELETE FROM tblborrowers WHERE borrowerId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $borrowerId);

    if ($stmt->execute()) {
        echo "Borrower deleted successfully!";
    } else {
        echo "Error deleting borrower!";
    }

    $stmt->close();
} else {
    echo "Invalid request!";
}
?>
