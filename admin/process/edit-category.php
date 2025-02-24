<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['categoryId']) || !isset($_POST['categoryName'])) {
        echo "Category ID and name are required.";
        exit;
    }

    $categoryId = intval($_POST['categoryId']);
    $categoryName = trim($_POST['categoryName']);

    if ($categoryId <= 0 || empty($categoryName)) {
        echo "Invalid category data.";
        exit;
    }

    try {
        $updateQuery = $conn->prepare("UPDATE tblcategory SET categoryName = ? WHERE categoryId = ?");
        $updateQuery->bind_param("si", $categoryName, $categoryId);

        if ($updateQuery->execute()) {
            echo "Category updated successfully!";
        } else {
            echo "Failed to update category.";
        }

        $updateQuery->close();
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
