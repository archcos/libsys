<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['categoryName']);

    if (!empty($categoryName)) {
        $query = $conn->prepare("INSERT INTO tblcategory (categoryName) VALUES (?)");
        $query->bind_param("s", $categoryName);

        if ($query->execute()) {
            echo "Category added successfully!";
        } else {
            echo "Failed to add category: " . $conn->error;
        }
    } else {
        echo "Category name cannot be empty!";
    }
} else {
    echo "Invalid request method!";
}
?>
