<?php
// Include the database connection
include('db-connect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form values
    $idNumber = $_POST['idNumber'];
    $borrowerType = $_POST['borrowerType'];
    $libraryId = $_POST['libraryId'];
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
    $sql = "UPDATE tblborrowers SET borrowerType = ?, libraryId = ?, surName = ?, firstName = ?, middleName = ?, course = ?, year = ?, position = ?, gender = ?, birthDate = ?, homeAddress = ?, remarks = ? WHERE idNumber = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssssssssss', 
        $borrowerType, $libraryId, $surName, $firstName, $middleName, 
        $course, $year, $position, $gender, $birthDate, $homeAddress, $remarks, $idNumber
    );
    
    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Success: Redirect back to the edit-borrower page with a success message
        header("Location: edit-borrower.php?idNumber=$idNumber&status=success");
        exit();
    } else {
        // Error: Redirect back to the edit-borrower page with an error message
        header("Location: edit-borrower.php?idNumber=$idNumber&status=error");
        exit();
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
