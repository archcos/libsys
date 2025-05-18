<?php
include('db-connect.php');

// Create book-author relationships table
$conn->query("
    CREATE TABLE IF NOT EXISTS tblbookauthor (
        bookId INT NOT NULL,
        authorId INT NOT NULL,
        PRIMARY KEY (bookId, authorId),
        FOREIGN KEY (bookId) REFERENCES tblbooks(bookId) ON DELETE CASCADE,
        FOREIGN KEY (authorId) REFERENCES tblauthor(authorId) ON DELETE CASCADE
    )
");

// Create book-category relationships table
$conn->query("
    CREATE TABLE IF NOT EXISTS tblbookcategory (
        bookId INT NOT NULL,
        categoryId INT NOT NULL,
        PRIMARY KEY (bookId, categoryId),
        FOREIGN KEY (bookId) REFERENCES tblbooks(bookId) ON DELETE CASCADE,
        FOREIGN KEY (categoryId) REFERENCES tblcategory(categoryId) ON DELETE CASCADE
    )
");

// Migrate existing book-author relationships
$conn->query("
    INSERT IGNORE INTO tblbookauthor (bookId, authorId)
    SELECT bookId, authorId FROM tblbooks WHERE authorId IS NOT NULL
");

// Migrate existing book-category relationships
$conn->query("
    INSERT IGNORE INTO tblbookcategory (bookId, categoryId)
    SELECT bookId, categoryId FROM tblbooks WHERE categoryId IS NOT NULL
");

echo "Database setup completed successfully!";
?> 