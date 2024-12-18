<?php
session_start();
include('db-connect.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = isset($_POST['courseName']) ? trim($_POST['courseName']) : '';
    $level = isset($_POST['level']) ? trim($_POST['level']) : '';

    // Validate the level input
    $validLevels = ['Undergraduate', 'Postgraduate', 'Doctoral'];
    if (!in_array($level, $validLevels)) {
        echo "Invalid level selected.";
        exit;
    }

    if (!empty($courseName) && !empty($level)) {
        $query = "INSERT INTO tblcourses (courseName, level) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $courseName, $level);

        if ($stmt->execute()) {
            echo "Course added successfully.";
        } else {
            echo "Error adding course: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Please provide all required fields.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
