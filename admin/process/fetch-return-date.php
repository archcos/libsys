<?php
// fetch-return-date.php

// Include database connection
include('db-connect.php');

// Check if the required POST data is available
if (isset($_POST['borrowerId']) && isset($_POST['bookId'])) {
    $borrowerId = $_POST['borrowerId'];
    $bookId = $_POST['bookId'];

    // Prepare SQL query to fetch the return date
    $query = "SELECT returnDate FROM tblreturnborrow WHERE borrowerId = ? AND bookId = ?";
    if ($stmt = $conn->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("ss", $borrowerId, $bookId);

        // Execute the query
        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($returnDate);
            $stmt->fetch();

            if ($returnDate) {
                // Calculate penalty (example)
                $currentDate = date('Y-m-d');
                $penalty = 0;

                if ($currentDate > $returnDate) {
                    // Calculate the penalty if the book is returned late
                    $date1 = new DateTime($currentDate);
                    $date2 = new DateTime($returnDate);
                    $diff = $date1->diff($date2);
                    $lateDays = $diff->days;

                    // Example penalty: $1 per day late
                    $penalty = $lateDays * 1;  // Adjust penalty as needed
                }

                // Return the data in JSON format
                echo json_encode([
                    'returnDate' => $returnDate,
                    'penalty' => $penalty
                ]);
            } else {
                echo json_encode(['error' => 'No return date found']);
            }
        } else {
            echo json_encode(['error' => 'Error executing query']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing query']);
    }
} else {
    echo json_encode(['error' => 'Required data not provided']);
}

// Close the connection
$conn->close();
?>
