<?php
include('../process/db-connect.php');

if (isset($_POST['bookId'])) {
    $bookId = $_POST['bookId'];
    
    $query = "
        SELECT b.callNum as callNumber, c.categoryName as category
        FROM tblbooks b
        JOIN tblcategory c ON b.categoryId = c.categoryId
        WHERE b.bookId = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode([
            'category' => $row['category'],
            'callNumber' => $row['callNumber']
        ]);
    } else {
        echo json_encode(['error' => 'Book not found']);
    }
}
?> 