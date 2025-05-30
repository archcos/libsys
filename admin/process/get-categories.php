<?php
include('db-connect.php');
header('Content-Type: application/json');
$result = $conn->query("SELECT categoryId, categoryName FROM tblcategory ORDER BY categoryName");
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
echo json_encode($categories);
$conn->close(); 