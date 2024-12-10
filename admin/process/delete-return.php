<?php
session_start();
include('db-connect.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrowId'])) {
    $borrowId = $_POST['borrowId'];

    // Delete the record from tblreturnborrow
    $deleteQuery = "DELETE FROM tblreturnborrow WHERE borrowId = ?";
    if ($stmt = $conn->prepare($deleteQuery)) {
        $stmt->bind_param("i", $borrowId);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Record deleted successfully.';
        } else {
            // Get the specific error message
            $response['message'] = 'Error deleting the record: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error preparing the delete query: ' . $conn->error;
    }
} else {
    $response['message'] = 'No borrowId provided.';
}

header('Content-Type: application/json');
echo json_encode($response);

?>
