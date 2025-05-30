<?php
include('db-connect.php');
header('Content-Type: application/json');
if (isset($_POST['categoryId'])) {
    $categoryId = $_POST['categoryId'];
    $stmt = $conn->prepare("SELECT bookId, title FROM tblbooks WHERE categoryId = ? ORDER BY title");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    echo json_encode($books);
    $stmt->close();
}
$conn->close(); 