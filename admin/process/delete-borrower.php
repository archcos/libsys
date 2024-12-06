<?php
include('../process/db-connect.php'); // Adjust the path to your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNumber = $_POST['idNumber']; // Get Borrower ID from POST request

    // Check if the borrower has unreturned books
    $checkQuery = "SELECT 1 FROM tblreturnborrow WHERE borrowerId = ? AND returned = 'No'";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('i', $idNumber); // Bind as integer
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If the borrower has unreturned books, deny deletion
        echo "This borrower cannot be deleted because they have unreturned books.";
    } else {
        // Proceed to delete the borrower
        $deleteQuery = "DELETE FROM tblborrowers WHERE idNumber = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $idNumber);

        if ($deleteStmt->execute()) {
            echo "Borrower deleted successfully!";
        } else {
            http_response_code(500);
            echo "Error: Unable to delete borrower.";
        }

        $deleteStmt->close();
    }

    $stmt->close();
    $conn->close();
}
?>
