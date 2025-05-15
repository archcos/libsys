<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

// Include database connection
include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $borrowerId = $_POST['borrowerId'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $callNumber = $_POST['callNumber'];
    $subLocation = $_POST['subLocation'];
    $date = $_POST['date'];
    $librarianName = $_SESSION['user_id']; // Get librarian's ID

    try {
        // Start transaction
        $conn->begin_transaction();

        // 1. Insert into tblreference
        $refStmt = $conn->prepare("INSERT INTO tblreference (borrowerId, type, title, author, category, callNumber, subLocation, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $refStmt->bind_param("isssssss", $borrowerId, $type, $title, $author, $category, $callNumber, $subLocation, $date);
        $refStmt->execute();

        if ($refStmt->affected_rows > 0) {
            // 2. Insert into tblreturnborrow
            $returnDate = date('Y-m-d', strtotime($date . ' + 7 days')); // Set return date to 7 days from reference date
            $borrowStmt = $conn->prepare("INSERT INTO tblreturnborrow (bookId, borrowerId, borrowedDate, librarianName, returnDate, returned) VALUES (?, ?, ?, ?, ?, 'No')");
            $borrowStmt->bind_param("iisss", $title, $borrowerId, $date, $librarianName, $returnDate);
            $borrowStmt->execute();

            // 3. Update book quantity
            $updateStmt = $conn->prepare("UPDATE tblbooks SET quantity = quantity - 1 WHERE bookId = ?");
            $updateStmt->bind_param("i", $title);
            $updateStmt->execute();

            // 4. Create notification
            // First get book title and borrower name
            $detailsQuery = "SELECT b.title, CONCAT(br.firstName, ' ', br.surName) as borrowerName 
                            FROM tblbooks b 
                            JOIN tblborrowers br ON br.idNumber = ? 
                            WHERE b.bookId = ?";
            $detailsStmt = $conn->prepare($detailsQuery);
            $detailsStmt->bind_param("ii", $borrowerId, $title);
            $detailsStmt->execute();
            $details = $detailsStmt->get_result()->fetch_assoc();

            // Create notification message
            $message = $details['borrowerName'] . " has borrowed the book: " . $details['title'];
            
            // Insert notification
            $notifStmt = $conn->prepare("INSERT INTO tblnotifications (borrowerId, bookId, message, status, type, remarks) VALUES (?, ?, ?, 'unread', 'borrow', 'Approved')");
            $notifStmt->bind_param("iis", $borrowerId, $title, $message);
            $notifStmt->execute();

            // Commit transaction
            $conn->commit();
            echo json_encode('Reference slip added successfully and book borrowed.');
        } else {
            throw new Exception('Failed to add reference slip.');
        }
    } catch (mysqli_sql_exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        if ($e->getCode() == 1452) {
            echo json_encode('The borrower ID provided does not exist. Please select a valid borrower.');
        } else {
            echo json_encode('An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
?>
