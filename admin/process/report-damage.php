<?php
// report-damage.php

// Include database connection
include('db-connect.php');

// Check if required POST data is available
if (isset($_POST['borrowerId']) && isset($_POST['bookId']) && isset($_POST['damageSeverity']) && isset($_POST['damageCost'])) {
    $borrowerId = $_POST['borrowerId'];
    $bookId = $_POST['bookId'];
    $damageSeverity = $_POST['damageSeverity'];
    $damageCost = $_POST['damageCost'];

    // Prepare SQL query to insert damage report
    $query = "INSERT INTO tblpenalties (borrowerId, bookId, penalty, cost) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("sssd", $borrowerId, $bookId, $damageSeverity, $damageCost);

        // Execute the query
        if ($stmt->execute()) {
            echo "Damage report saved successfully.";
        } else {
            echo "Error saving damage report: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
} else {
    echo "Required data not provided.";
}

// Close the connection
$conn->close();
?>
