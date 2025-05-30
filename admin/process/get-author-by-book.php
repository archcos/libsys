<?php
include('db-connect.php');
header('Content-Type: application/json');
if (isset($_POST['bookId'])) {
    $bookId = $_POST['bookId'];
    $stmt = $conn->prepare("SELECT a.authorId, CONCAT(a.firstName, ' ', a.lastName) AS authorName FROM tblbooks b JOIN tblauthor a ON b.authorId = a.authorId WHERE b.bookId = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['authorId' => $row['authorId'], 'authorName' => $row['authorName']]);
    } else {
        echo json_encode(['authorId' => '', 'authorName' => '']);
    }
    $stmt->close();
}
$conn->close(); 