<?php
// Include your database connection file
include('db-connect.php'); // Adjust the path to your actual connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $borrowerType = $_POST['borrowerType'] ?? '';
    $libraryId = $_POST['libraryId'] ?? '';
    $facultyId = $_POST['facultyId'] ?? null; // Optional, might not be present for Students
    $surName = $_POST['surName'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? ''; // Optional
    $course = $_POST['course'] ?? null; // Optional, might not be present for Faculty/Staff
    $year = $_POST['year'] ?? null; // Optional, might not be present for Faculty/Staff
    $position = $_POST['position'] ?? null; // Optional, might not be present for Students
    $gender = $_POST['gender'] ?? '';
    $birthDate = $_POST['birthDate'] ?? null; // Optional
    $homeAddress = $_POST['homeAddress'] ?? ''; // Optional
    $remarks = 1; // Default value for remarks

    // Validate mandatory fields
    if (empty($libraryId) || empty($surName) || empty($firstName) || empty($gender)) {
        die('Error: Please fill in all required fields.');
    }

    // Insert data into the database
    $query = "INSERT INTO tblborrowers (
        libraryId, facultyId, surName, firstName, middleName, course, year, position, gender, birthDate, homeAddress, remarks
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        'sssssssssssi',
        $libraryId,
        $facultyId,
        $surName,
        $firstName,
        $middleName,
        $course,
        $year,
        $position,
        $gender,
        $birthDate,
        $homeAddress,
        $remarks
    );

    if ($stmt->execute()) {
        // Redirect to the form page with a success status
        header("Location: add-borrower.php?borrowerType=$borrowerType&status=success");
        exit(); // Make sure to call exit to stop further code execution
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
