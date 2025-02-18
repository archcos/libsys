<?php
include('../process/db-connect.php');

if (isset($_POST['authorId'])) {
    $authorId = $_POST['authorId'];
    $query = "SELECT bookId, title FROM tblbooks WHERE authorId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $authorId);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    
    echo json_encode($books);
}
?>
