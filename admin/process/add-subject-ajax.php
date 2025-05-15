<?php
include('db-connect.php');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate the input
$categoryName = trim($_POST['categoryName'] ?? '');

if (empty($categoryName)) {
    echo json_encode(['success' => false, 'message' => 'Subject name is required']);
    exit;
}

// Check if category already exists
$checkQuery = "SELECT categoryId FROM tblcategory WHERE categoryName = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $categoryName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Subject already exists']);
    exit;
}

// Insert the new category
$query = "INSERT INTO tblcategory (categoryName) VALUES (?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $categoryName);

if ($stmt->execute()) {
    $categoryId = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'categoryId' => $categoryId,
        'message' => 'Subject added successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding subject: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close(); 