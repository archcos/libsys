<?php

include('../db/db-connect.php');

// Load PHPMailer
require '../../admin/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNumber = $conn->real_escape_string($_POST['idNumber']);
    $borrowerType = $conn->real_escape_string($_POST['borrowerType']);
    $surName = $conn->real_escape_string($_POST['surName']);
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $middleName = $conn->real_escape_string($_POST['middleName']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $position = isset($_POST['position']) ? $conn->real_escape_string($_POST['position']) : null;
    $course = isset($_POST['courseId']) ? $conn->real_escape_string($_POST['courseId']) : 0;
    $year = isset($_POST['year']) ? $conn->real_escape_string($_POST['year']) : null;
    $gender = $conn->real_escape_string($_POST['gender']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);
    $remarks = $conn->real_escape_string('Deactivated');
    $homeAddress = $conn->real_escape_string($_POST['homeAddress']);
    $librarian = isset($_POST['librarian']) ? $conn->real_escape_string($_POST['librarian']) : "Account Created in Borrower Page.";
    $reason = isset($_POST['reason']) ? $conn->real_escape_string($_POST['reason']) : "Account Created in Borrower Page.";


    // Check if the ID number already exists
    $query = "SELECT 1 FROM tblborrowers WHERE idNumber = '$idNumber' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // ID already exists, redirect back with an error
        header("Location: ../registration.php?borrowerType=$borrowerType&status=exists");
        exit;
    }

    // Insert new borrower into the database
    $query = "INSERT INTO tblborrowers (idNumber, surName, firstName, middleName, emailAddress, position, course, year, gender, birthDate, remarks, homeAddress, borrowerType, librarian, reason) 
              VALUES ('$idNumber', '$surName', '$firstName', '$middleName', '$emailAddress', '$position', '$course', '$year', '$gender', '$birthDate', '$remarks', '$homeAddress', '$borrowerType', '$librarian', '$reason')";
    
    if ($conn->query($query)) {
        // Initialize PHPMailer for email notifications
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mjaymillanar@gmail.com';
            $mail->Password = 'glkw yaay tmuq zzmd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('mjaymillanar@gmail.com', 'Library Notification');

            // Set recipient email and message
            $mail->clearAddresses();
            $mail->addAddress($emailAddress);

            $mail->isHTML(true);
            $mail->Subject = 'USTP Library - Registration Confirmation';
            $mail->Body = "
                <h1>Hello, $firstName!</h1>
                <p>Thank you for registering at USTP Library. Your registration details: ID Number: $idNumber, Name: $firstName $middleName $surName, Borrower Type: $borrowerType. <strong>Important:</strong> Please visit the library to get your Borrower's Card. Thank you!</p>
                <hr>
                <p style='font-size:12px;color:#888;'>
                    <strong>Data Privacy Disclaimer:</strong> This email and any information contained herein are intended solely for the registered recipient. By using the USTP Library System, you acknowledge and accept our data privacy policy. Your personal data is processed in accordance with applicable laws and is used only for library-related transactions. If you received this email in error, please notify us and delete it immediately.
                </p>
            ";
            $mail->AltBody = "Hello $firstName, Thank you for registering at USTP Library. Your registration details: ID Number: $idNumber, Name: $firstName $middleName $surName, Borrower Type: $borrowerType. Important: Please visit the library to get your Borrower's Card. Thank you!\n\nData Privacy Disclaimer: This email and any information contained herein are intended solely for the registered recipient. By using the USTP Library System, you acknowledge and accept our data privacy policy. Your personal data is processed in accordance with applicable laws and is used only for library-related transactions. If you received this email in error, please notify us and delete it immediately.";

            $mail->send();
            // Success, redirect to login page
            header("Location: ../login.php?borrowerType=$borrowerType&status=success");
        } catch (Exception $e) {
            // Even if email fails, still redirect to login since registration was successful
            header("Location: ../login.php?borrowerType=$borrowerType&status=success");
        }
    } else {
        // Error inserting borrower
        header("Location: ../registration.php?borrowerType=$borrowerType&status=error");
    }

    $conn->close();
    exit;
}
?>
