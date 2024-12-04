<?php
include('db-connect.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNumber = $_POST['idNumber'];
    $receipt = $_POST['receipt'];

    // Validate receipt value
    if (!in_array($receipt, ['Yes', 'No'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid receipt value']);
        exit;
    }

    // Update the database
    $query = "UPDATE tblborrowers SET receipt = ? WHERE idNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $receipt, $idNumber);

    if ($stmt->execute()) {
        http_response_code(200); // Success
    } else {
        http_response_code(500); // Internal server error
    }
}
?>
