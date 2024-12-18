<?php
session_start();
include('db-connect.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = isset($_POST['courseId']) ? intval($_POST['courseId']) : 0;
    $courseName = isset($_POST['courseName']) ? trim($_POST['courseName']) : '';
    $level = isset($_POST['level']) ? trim($_POST['level']) : '';

    if ($courseId > 0 && !empty($courseName) && !empty($level)) {
        $query = "UPDATE tblcourses SET courseName = ?, level = ? WHERE courseId = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $courseName, $level, $courseId);

        if ($stmt->execute()) {
            echo "Course updated successfully.";
        } else {
            echo "Error updating course: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Invalid input data.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
