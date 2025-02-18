<?php
include('../process/db-connect.php');

if (isset($_POST['bookId'])) {
    $bookId = $_POST['bookId'];
    $query = "
        SELECT c.categoryName 
        FROM tblbooks b 
        JOIN tblcategory c ON b.categoryId = c.categoryId 
        WHERE b.bookId = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['category' => $row['categoryName']]);
    }
}
?>
