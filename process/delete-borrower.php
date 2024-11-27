<?php
include('../process/db-connect.php'); // Adjust the path to your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNumber = $_POST['idNumber']; // Get Library ID from POST request

    // Prepare and execute delete query
    $query = "DELETE FROM tblborrowers WHERE idNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $idNumber); // Bind as integer

    if ($stmt->execute()) {
        echo "Borrower deleted successfully!";
    } else {
        http_response_code(500);
        echo "Error: Unable to delete borrower.";
    }

    $stmt->close();
    $conn->close();
}
?>
