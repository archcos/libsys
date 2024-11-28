<?php
// Include your database connection file
include('../process/db-connect.php');

// Get POST data
$bookId = $_POST['bookId'];
$idNumber = $_POST['idNumber'];
$returnDate = $_POST['returnDate'];

// Check if idNumber exists in tblborrowers
$checkBorrower = "SELECT * FROM tblborrowers WHERE idNumber = ?";
$stmt = $conn->prepare($checkBorrower);
$stmt->bind_param("s", $idNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Error: Borrower ID not found.";
    exit;
}

// Add the record to tblreturnborrow
$insertQuery = "INSERT INTO tblreturnborrow (bookId, borrowerId, returnDate) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iis", $bookId, $idNumber, $returnDate);
$insertResult = $stmt->execute();

if ($insertResult) {
    // Update availability in tblbooks
    $updateQuery = "UPDATE tblbooks SET available = 'No' WHERE bookId = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();

    echo "Book borrowed successfully!";
} else {
    echo "Error: Could not borrow the book. Please try again.";
}

$stmt->close();
$conn->close();
?>