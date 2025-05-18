-- Create table for book-author relationships
CREATE TABLE IF NOT EXISTS tblbookauthor (
    bookId INT NOT NULL,
    authorId INT NOT NULL,
    PRIMARY KEY (bookId, authorId),
    FOREIGN KEY (bookId) REFERENCES tblbooks(bookId) ON DELETE CASCADE,
    FOREIGN KEY (authorId) REFERENCES tblauthor(authorId) ON DELETE CASCADE
);

-- Create table for book-category relationships
CREATE TABLE IF NOT EXISTS tblbookcategory (
    bookId INT NOT NULL,
    categoryId INT NOT NULL,
    PRIMARY KEY (bookId, categoryId),
    FOREIGN KEY (bookId) REFERENCES tblbooks(bookId) ON DELETE CASCADE,
    FOREIGN KEY (categoryId) REFERENCES tblcategory(categoryId) ON DELETE CASCADE
);

-- Migrate existing book-author relationships
INSERT INTO tblbookauthor (bookId, authorId)
SELECT bookId, authorId FROM tblbooks WHERE authorId IS NOT NULL;

-- Migrate existing book-category relationships
INSERT INTO tblbookcategory (bookId, categoryId)
SELECT bookId, categoryId FROM tblbooks WHERE categoryId IS NOT NULL; 