<?php
session_start();
// Include your database connection file
include('../process/db-connect.php');

// Get POST data
$bookId = $_POST['bookId'];
$notificationId = $_POST['notificationId'];
$idNumber = $_POST['idNumber'];
$librarianName = $_POST['librarianName'];
$returnDate = $_POST['returnDate'];

// Check if borrower exists in tblborrowers
$checkBorrower = "SELECT * FROM tblborrowers WHERE idNumber = ?";
$stmt = $conn->prepare($checkBorrower);
$stmt->bind_param("s", $idNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Error: Borrower ID not found.";
    exit;
}

// Check if borrower has already borrowed this specific book and not returned it
$checkBookLoan = "SELECT * FROM tblreturnborrow WHERE borrowerId = ? AND bookId = ? AND returned = 'No'";
$stmt = $conn->prepare($checkBookLoan);
$stmt->bind_param("si", $idNumber, $bookId);
$stmt->execute();
$loanResult = $stmt->get_result();

if ($loanResult->num_rows > 0) {
    echo "Borrower already borrowed the book and hasn't been returned.";
    exit;
}


// Insert a new record into tblreturnborrow
$insertQuery = "INSERT INTO tblreturnborrow (bookId, borrowerId, borrowedDate, librarianName, returnDate, returned) 
                VALUES (?, ?, NOW(), ?, ?, 'No')";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iiss", $bookId, $idNumber, $librarianName, $returnDate);
$insertResult = $stmt->execute();

$insertQuery = "INSERT INTO tblreturnborrow (bookId, borrowerId, borrowedDate, librarianName, returnDate, returned) 
                VALUES (?, ?, NOW(), ?, ?, 'No')";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iiss", $bookId, $idNumber, $librarianName, $returnDate);
$insertResult = $stmt->execute();


if ($insertResult) {
    // Decrease quantity and update availability in tblbooks
    $updateQuery = "UPDATE tblbooks 
                    SET quantity = quantity - 1
                    WHERE bookId = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();

    $updateNotif = "UPDATE tblnotifications 
                    SET remarks = 'Approved'
                    WHERE notificationId = ?";
    $stmt = $conn->prepare($updateNotif);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();

    echo "Book borrowed successfully!";
} else {
    echo "Error: Could not borrow the book. Please try again.";
}

// Close connection and statement
$stmt->close();
$conn->close();
?>
