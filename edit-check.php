<?php
// Include your database connection
include('db-connect.php');

// Check if Borrower ID is passed
if (isset($_POST['borrowerId'])) {
    $borrowerId = $_POST['borrowerId'];

    // Query to check if the Borrower ID exists in the database
    $sql = "SELECT * FROM tblborrowers WHERE borrowerId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $borrowerId); // Bind the Borrower ID to the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any row is returned
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]); // Borrower ID exists
    } else {
        echo json_encode(['exists' => false]); // Borrower ID does not exist
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // If no Borrower ID sent
    echo json_encode(['exists' => false]);
}
?>
