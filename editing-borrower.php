<?php
// Include the database connection
include('db-connect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form values
    $borrowerId = $_POST['borrowerId'];
    $borrowerType = $_POST['borrowerType'];
    $libraryId = $_POST['libraryId'];
    $facultyId = $_POST['facultyId'];
    $surName = $_POST['surName'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $position = $_POST['position'];
    $gender = $_POST['gender'];
    $birthDate = $_POST['birthDate'];
    $homeAddress = $_POST['homeAddress'];
    $remarks = $_POST['remarks'];

    // Prepare the update query
    $sql = "UPDATE tblborrowers SET borrowerType = ?, libraryId = ?, facultyId = ?, surName = ?, firstName = ?, middleName = ?, course = ?, year = ?, position = ?, gender = ?, birthDate = ?, homeAddress = ?, remarks = ? WHERE borrowerId = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssssssss', 
        $borrowerType, $libraryId, $facultyId, $surName, $firstName, $middleName, 
        $course, $year, $position, $gender, $birthDate, $homeAddress, $remarks, $borrowerId
    );
    
    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        echo "Borrower data updated successfully!";
    } else {
        echo "Error updating borrower data.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
