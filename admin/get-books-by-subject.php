<?php
include('process/db-connect.php');

$subject = $_GET['subject'] ?? null;

if ($subject) {
    // Fetch books for the selected subject
    $booksQuery = "SELECT b.bookId, b.title, CONCAT(a.firstName, ' ', a.lastName) as authorName 
                  FROM tblbooks b 
                  JOIN tblauthor a ON b.authorId = a.authorId 
                  WHERE b.category = ?";
    $stmt = $conn->prepare($booksQuery);
    $stmt->bind_param("s", $subject);
    $stmt->execute();
    $booksResult = $stmt->get_result();

    $books = [];
    if ($booksResult->num_rows > 0) {
        while ($book = $booksResult->fetch_assoc()) {
            $books[] = [
                'bookId' => $book['bookId'],
                'title' => $book['title'],
                'authorName' => $book['authorName']
            ];
        }
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($books);
}
?> 