<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not authorized']));
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['categoryName']);

    if (empty($categoryName)) {
        die(json_encode(['success' => false, 'message' => 'Category name is required']));
    }

    // Check if category already exists
    $checkQuery = "SELECT categoryId FROM tblcategory WHERE categoryName = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die(json_encode(['success' => false, 'message' => 'Category already exists']));
    }

    // Insert new category
    $query = "INSERT INTO tblcategory (categoryName) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $categoryName);

    if ($stmt->execute()) {
        $categoryId = $stmt->insert_id;
        echo json_encode([
            'success' => true,
            'categoryId' => $categoryId,
            'message' => 'Category added successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error adding category'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 