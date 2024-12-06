<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit;
}

include('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure categoryId is provided
    if (!isset($_POST['categoryId'])) {
        echo "Category ID is required.";
        exit;
    }

    $categoryId = intval($_POST['categoryId']);

    if ($categoryId <= 0) {
        echo "Invalid category ID.";
        exit;
    }

    try {
        // Check if the category is associated with any books in tblbooks
        $checkQuery = $conn->prepare("SELECT COUNT(*) AS count FROM tblbooks WHERE categoryId = ?");
        $checkQuery->bind_param("i", $categoryId);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();
        $row = $checkResult->fetch_assoc();
        $checkQuery->close();

        if ($row['count'] > 0) {
            echo "Cannot delete category. This category is associated with one or more books.";
        } else {
            // Proceed to delete the category if not associated with any books
            $deleteQuery = $conn->prepare("DELETE FROM tblcategory WHERE categoryId = ?");
            $deleteQuery->bind_param("i", $categoryId);
            if ($deleteQuery->execute()) {
                echo "Category deleted successfully!";
            } else {
                echo "Failed to delete category.";
            }
            $deleteQuery->close();
        }
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
