<?php
include('db-connect.php');

// Fetch notifications with combined full names for authors and borrowers
$sql = "
    SELECT 
        n.notificationId,
        n.borrowerId,
        n.bookId,
        n.message,
        n.status,
        n.type,
        n.timestamp,
        b.title AS bookTitle,
        CONCAT(a.firstName, ' ', a.lastName) AS authorFullName,
        CONCAT(br.firstName, ' ', br.surName) AS borrowerFullName
    FROM tblnotifications n
    JOIN tblbooks b ON n.bookId = b.bookId
    JOIN tblauthor a ON b.authorId = a.authorId
    JOIN tblborrowers br ON n.borrowerId = br.idNumber
    ORDER BY n.notificationId DESC
";

$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format the timestamp
        $dateTime = new DateTime($row['timestamp']);
        $formattedTimestamp = $dateTime->format('M j, Y g:i A'); // e.g., "Dec 6, 2024 2:30 PM"

        $notifications[] = [
            'notificationId' => $row['notificationId'],
            'borrowerId' => $row['borrowerId'],
            'bookId' => $row['bookId'],
            'message' => htmlspecialchars($row['message']),
            'status' => $row['status'],
            'type' => $row['type'],
            'timestamp' => $formattedTimestamp, // Use formatted timestamp
            'bookTitle' => $row['bookTitle'],
            'authorFullName' => $row['authorFullName'], // Author's full name
            'borrowerFullName' => $row['borrowerFullName'] // Borrower's full name
        ];
    }
}

// Return notifications as JSON
echo json_encode(['notifications' => $notifications]);
$conn->close();
?>
