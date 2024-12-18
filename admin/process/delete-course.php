<?php
session_start();
include('db-connect.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = isset($_POST['courseId']) ? intval($_POST['courseId']) : 0;

    if ($courseId > 0) {
        $query = "DELETE FROM tblcourses WHERE courseId = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $courseId);

        if ($stmt->execute()) {
            echo "Course deleted successfully.";
        } else {
            echo "Error deleting course: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Invalid course ID.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
