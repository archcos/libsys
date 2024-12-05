<?php

include('../db/db-connect.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNumber = $conn->real_escape_string($_POST['idNumber']);
    $borrowerType = $conn->real_escape_string($_POST['borrowerType']);
    $libraryId = $conn->real_escape_string($_POST['libraryId']);
    $surName = $conn->real_escape_string($_POST['surName']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $middleName = $conn->real_escape_string($_POST['middleName']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $position = isset($_POST['position']) ? $conn->real_escape_string($_POST['position']) : null;
    $course = isset($_POST['course']) ? $conn->real_escape_string($_POST['course']) : null;
    $year = isset($_POST['year']) ? $conn->real_escape_string($_POST['year']) : null;
    $gender = $conn->real_escape_string($_POST['gender']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);
    $homeAddress = $conn->real_escape_string($_POST['homeAddress']);

    // Check if the ID number already exists
    $query = "SELECT 1 FROM tblborrowers WHERE idNumber = '$idNumber' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // ID already exists, redirect back with an error
        header("Location: ../registration.php?borrowerType=$borrowerType&status=exists");
        exit;
    }

    // Insert new borrower into the database
    $query = "INSERT INTO tblborrowers (idNumber, libraryId, surName, firstName, middleName, emailAddress, position, course, year, gender, birthDate, homeAddress, borrowerType) 
              VALUES ('$idNumber', '$libraryId', '$surName', '$firstName', '$middleName', '$emailAddress', '$position', '$course', '$year', '$gender', '$birthDate', '$homeAddress', '$borrowerType')";

    if ($conn->query($query)) {
        // Success, redirect back with a success message
        header("Location: ../login.php?borrowerType=$borrowerType&status=success");
    } else {
        // Error inserting borrower
        header("Location: ../registration.php?borrowerType=$borrowerType&status=error");
    }

    $conn->close();
    exit;
}
?>
