<?php
// Include your database connection
include('db-connect.php');

// Check if the necessary POST data is received
if (isset($_POST['idNumber'])) {
    $idNumber = $_POST['idNumber'];

    // Delete the borrower
    $query = "DELETE FROM tblborrowers WHERE idNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idNumber);

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
