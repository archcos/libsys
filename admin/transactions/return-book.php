<?php
session_start();
// Include database connection file
include('../process/db-connect.php');

if (isset($_POST['bookId']) && isset($_POST['idNumber'])) {
    $bookId = intval($_POST['bookId']); // Sanitize input
    $idNumber = intval($_POST['idNumber']); // Sanitize input
    $notificationId = $_POST['notificationId'];


    // Step 1: Check if the idNumber and bookId exist in tblreturnborrow with "returned = 'No'"
    $checkQuery = "SELECT * FROM tblreturnborrow WHERE bookId = ? AND borrowerId = ? AND returned = 'No'";
    $stmtCheck = $conn->prepare($checkQuery);
    if ($stmtCheck) {
        $stmtCheck->bind_param("ii", $bookId, $idNumber);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows === 0) {
            // No record found for the given bookId and idNumber
            echo "Error: This borrower did not borrow this book, or it has already been returned.";
            $stmtCheck->close();
            $conn->close();
            exit;
        }
        $stmtCheck->close();
    } else {
        echo "Error: Failed to prepare the check query.";
        $conn->close();
        exit;
    }

    $updateNotif = "UPDATE tblnotifications 
        SET remarks = 'Approved'
        WHERE notificationId = ?";
    $stmt = $conn->prepare($updateNotif);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();

    // Step 2: Proceed to mark the book as returned
    $updateReturn = "UPDATE tblreturnborrow SET returned = 'Yes' WHERE bookId = ? AND borrowerId = ?";
    $stmt1 = $conn->prepare($updateReturn);


    if ($stmt1) {
        $stmt1->bind_param("ii", $bookId, $idNumber);

        // Update the available quantity in tblbooks
        $updateAvailable = "UPDATE tblbooks SET quantity = quantity + 1 WHERE bookId = ?";
        $stmt2 = $conn->prepare($updateAvailable);

        if ($stmt2) {
            $stmt2->bind_param("i", $bookId);

            if ($stmt1->execute() && $stmt2->execute()) {
                echo "Book successfully marked as returned.";
            } else {
                echo "Error: Could not mark the book as returned.";
            }

            $stmt2->close();
        } else {
            echo "Error: Failed to prepare the update query for tblbooks.";
        }

        $stmt1->close();
    } else {
        echo "Error: Failed to prepare the update query for tblreturnborrow.";
    }
} else {
    echo "Invalid request. Missing book ID or ID number.";
}

// Close the database connection
$conn->close();
?>
