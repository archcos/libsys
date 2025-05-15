<?php
include('db-connect.php');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate the input
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');

if (empty($lastName)) {
    echo json_encode(['success' => false, 'message' => 'Last name is required']);
    exit;
}

// Insert the new author
$query = "INSERT INTO tblauthor (firstName, lastName) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $firstName, $lastName);

if ($stmt->execute()) {
    $authorId = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'authorId' => $authorId,
        'message' => 'Author added successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding author: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close(); 