<?php
include('db-connect.php');

$categoryId = $_GET['categoryId'] ?? null;
$authorId = $_GET['authorId'] ?? null;

// Build the query dynamically based on the filter
$query = "SELECT b.bookId, b.title, b.quantity, 
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName 
          FROM tblbooks b
          JOIN tblauthor a ON b.authorId = a.authorId
          JOIN tblcategory c ON b.categoryId = c.categoryId";

if ($categoryId) {
    $query .= " WHERE b.categoryId = ?";
} elseif ($authorId) {
    $query .= " WHERE b.authorId = ?";
}

$stmt = $conn->prepare($query);

if ($categoryId) {
    $stmt->bind_param('i', $categoryId);
} elseif ($authorId) {
    $stmt->bind_param('i', $authorId);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['bookId']}</td>";
    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
    echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
    echo "<td>{$row['quantity']}</td>";
    echo "<td>
            <button class='delete-btn' data-book-id='{$row['bookId']}'>Delete</button>
          </td>";
    echo "</tr>";
}
?>
