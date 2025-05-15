<?php
session_start(); // Start the session
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1);    // Enable error logging
ini_set('error_log', __DIR__ . '/php-error.log'); // Log errors to a file

// Include the database connection
require '../db/db-connect.php'; // Adjust the path if needed

// Load PHPMailer
require '../../admin/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'idNumber' exists in the POST data
    if (empty($_POST['idNumber'])) {
        echo json_encode(['success' => false, 'message' => 'ID number is required.']);
        exit();
    }

    $idNumber = $_POST['idNumber'];  // Retrieve the ID number directly from POST data

    try {
        // Query the database using mysqli
        $stmt = $conn->prepare('SELECT * FROM tblborrowers WHERE idNumber = ?');
        $stmt->bind_param('s', $idNumber); // 's' means the parameter is a string (ID number)
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check the `remarks` field
            if ($user['remarks'] == 'Activated') {
                // Allow login
                $_SESSION['user_id'] = $user['idNumber'];  // Store ID number in session (or user ID if needed)
                $_SESSION['username'] = $user['firstName'] . ' ' . $user['surName'];  // Full name
                $_SESSION['borrowerType'] = $user['borrowerType'];  // Borrower type if necessary

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
                    $mail->addAddress($user['emailAddress']);

                    $mail->isHTML(true);
                    $mail->Subject = 'USTP Library - Login Notification';
                    $mail->Body = "
                        <h1>Hello, {$user['firstName']}!</h1>
                        <p>This is to notify you that your account was just used to log in to the USTP Library System.</p>
                        <p>Login Details:</p>
                        <ul>
                            <li><strong>ID Number:</strong> {$user['idNumber']}</li>
                            <li><strong>Name:</strong> {$user['firstName']} {$user['middleName']} {$user['surName']}</li>
                            <li><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</li>
                        </ul>
                        <p>If this wasn't you, please contact the library immediately.</p>
                        <p>Thank you!</p>
                    ";
                    $mail->AltBody = "Hello {$user['firstName']}, Your account was just used to log in to the USTP Library System. If this wasn't you, please contact the library immediately.";

                    $mail->send();
                } catch (Exception $e) {
                    // Continue with login even if email fails
                }

                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                // Account is deactivated
                echo json_encode([
                    'success' => false, 
                    'message' => "Your account is currently deactivated. Please contact the Librarian."
                ]);
            }
        } else {
            // ID number not found
            echo json_encode(['success' => false, 'message' => 'Invalid ID Number. Please register first using your ID Number.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        // Log the error and send a generic response
        error_log('Error in login.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An internal error occurred.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
