<?php
// Start session to store success message
$redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../dashboard.php';


// Load Composer's autoloader
require '../vendor/autoload.php'; // Ensure the path to 'vendor/autoload.php' is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include database connection
include('db-connect.php');

// Get today's date
$today = new DateTime();
$todayFormatted = $today->format('Y-m-d');

// Fetch data for books due in 2 days or less and not returned
$sql = "
    SELECT rb.borrowerId, rb.bookId, rb.returnDate, b.emailAddress, b.firstname
    FROM tblreturnborrow rb
    INNER JOIN tblborrowers b ON rb.borrowerId = b.idNumber
    WHERE rb.returned = 'No'
    AND DATEDIFF(rb.returnDate, ?) <= 2;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $todayFormatted);
$stmt->execute();
$result = $stmt->get_result();

// Initialize PHPMailer
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'joedavid1345@gmail.com'; // Your email address
$mail->Password = 'ogdo xldv yyla gley';   // Your app-specific password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->setFrom('joedavid1345@gmail.com', 'Library Notification');

// Check if there are borrowers who have books due soon
if ($result->num_rows > 0) {
    // Loop through each borrower and send an email
    $emailsSent = 0;
    $emailsFailed = 0;
    
    while ($row = $result->fetch_assoc()) {
        $email = $row['emailAddress'];
        $firstname = $row['firstname'];
        $returnDate = $row['returnDate'];

        // Set recipient email and message
        $mail->clearAddresses(); // Clear previous recipient
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Library Book Return Reminder';
        $mail->Body = "
            <h1>Hello, $firstname!</h1>
            <p>This is a reminder that the book you borrowed is due on <strong>$returnDate</strong>.</p>
            <p>Please return it to the library to avoid penalties.</p>
            <p>Thank you!</p>
        ";
        $mail->AltBody = "Hello $firstname, this is a reminder that the book you borrowed is due on $returnDate. Please return it to the library.";

        // Send the email
        if ($mail->send()) {
            $emailsSent++;
        } else {
            $emailsFailed++;
        }
    }
    
    // Set success or failure message in session
    if ($emailsSent > 0) {
        // Redirect with status 'success'
        header("Location: $redirectUrl?status=success");
        exit();
    }
    if ($emailsFailed > 0) {
        header("Location: $redirectUrl?status=error");
        exit();
    }
} else {
    header("Location: $redirectUrl?status=noreturn");
    exit();

}

// Close connection
$stmt->close();
$conn->close();

// Redirect back to the previous page (for example, `due-date.php`)
header('Location: ../dashboard.php');
exit;
?>
