<?php
include('../process/db-connect.php');  // Include your DB connection

if (isset($_POST['borrowerId'])) {
    $borrowerId = $_POST['borrowerId'];

    // Query to fetch borrower type
    $query = "SELECT borrowerType FROM tblborrowers WHERE idNumber = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $borrowerId);
        $stmt->execute();
        $stmt->bind_result($borrowerType);
        $stmt->fetch();
        $stmt->close();
        
        // Return the borrower type (e.g., 'Student', 'Faculty', etc.)
        echo $borrowerType;
    } else {
        echo 'Error fetching borrower type';
    }
} else {
    echo 'No borrower ID provided';
}
?>
