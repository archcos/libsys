<?php
include('process/db-connect.php');

$authorId = $_GET['authorId'] ?? null;

if ($authorId) {
    // Fetch books for the selected author
    $booksQuery = "SELECT bookId, title FROM tblbooks WHERE authorId = ?";
    $stmt = $conn->prepare($booksQuery);
    $stmt->bind_param("i", $authorId);
    $stmt->execute();
    $booksResult = $stmt->get_result();

    // Return the book options as HTML
    if ($booksResult->num_rows > 0) {
        while ($book = $booksResult->fetch_assoc()) {
            echo "<option value='" . $book['bookId'] . "'>" . htmlspecialchars($book['title']) . "</option>";
        }
    } else {
        echo "<option value=''>No books available</option>";
    }
}
?>
