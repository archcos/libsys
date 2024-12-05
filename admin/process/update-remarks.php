<?php
include('db-connect.php'); // Adjust the path to your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNumber = $_POST['idNumber']; // Get Library ID from POST request
    $remarks = $_POST['remarks']; // Get Remarks value (1 for Activated, 0 for Deactivated)

    // Prepare and execute update query
    $query = "UPDATE tblborrowers SET remarks = ? WHERE idNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $remarks, $idNumber); 

    if ($stmt->execute()) {
        echo "Remarks updated successfully!";
    } else {
        http_response_code(500);
        echo "Error: Unable to update remarks.";
    }

    $stmt->close();
    $conn->close();
}
?>
