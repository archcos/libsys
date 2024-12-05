<?php
// Include database connection
include('db-connect.php');

// Check if bookId is provided via POST request
if (isset($_POST['bookId']) && !empty($_POST['bookId'])) {
    // Get the bookId from the POST request
    $bookId = $_POST['bookId'];

    // Prepare SQL query to delete the book by its ID
    $query = "DELETE FROM tblbooks WHERE bookId = ?";

    // Initialize prepared statement
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("i", $bookId); // "i" stands for integer

        // Execute the query
        if ($stmt->execute()) {
            // If the query was successful, return a success message
            echo "Book deleted successfully!";
        } else {
            // If there was an error executing the query
            echo "Error deleting book. Please try again.";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // If the prepared statement could not be initialized
        echo "Error preparing the query.";
    }
} else {
    // If bookId is not provided
    echo "No book ID provided.";
}

// Close the database connection
$conn->close();
?>
