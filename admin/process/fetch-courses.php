<?php
include('db-connect.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level = isset($_POST['level']) ? $_POST['level'] : '';

    // Validate level input
    $validLevels = ['Undergraduate', 'Postgraduate', 'Doctoral'];
    if (!in_array($level, $validLevels)) {
        echo '<option value="">Invalid level</option>';
        exit;
    }

    // Fetch courses for the selected level
    $query = "SELECT courseId, courseName FROM tblcourses WHERE level = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $level);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate options for the course dropdown
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['courseId']) . '">' . htmlspecialchars($row['courseName']) . '</option>';
        }
    } else {
        echo '<option value="">No courses available</option>';
    }

    $stmt->close();
}

$conn->close();
?>
