<?php
include('db-connect.php'); // Adjust the path to your database connection file

var_dump($_POST); // Check the received POST data
$idNumber = $_POST['idNumber'] ?? null;
$remarks = $_POST['remarks'] ?? null;

if ($idNumber === null || $remarks === null) {
    echo "Missing idNumber or remarks!";
    exit;
}

// Proceed with database query
include('db-connect.php');
$query = "UPDATE tblborrowers SET remarks = ? WHERE idNumber = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $remarks, $idNumber); // First parameter is now a string

if ($stmt->execute()) {
    echo "Update successful for ID $idNumber with remarks $remarks";
} else {
    echo "Database update failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
