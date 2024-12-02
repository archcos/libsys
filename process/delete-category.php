<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = intval($_POST['categoryId']);

    if ($categoryId > 0) {
        $query = $conn->prepare("DELETE FROM tblcategory WHERE categoryId = ?");
        $query->bind_param("i", $categoryId);

        if ($query->execute()) {
            echo "Category deleted successfully!";
        } else {
            echo "Failed to delete category: " . $conn->error;
        }
    } else {
        echo "Invalid category ID!";
    }
} else {
    echo "Invalid request method!";
}
?>
