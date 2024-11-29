<?php
// Database connection (update with your credentials)
include('../process/db-connect.php'); // Adjust the path to your actual connection file

// Get the current date (use 'Y-m-d' for date comparison without the time part)
$currentDateTime = date('Y-m-d');

// Query the database to find all records with a matching return_date
$sql = "SELECT * FROM auto WHERE return_date = :return_date";
$stmt = $pdo->prepare($sql);

// Bind the parameter explicitly
$stmt->bindParam(':return_date', $currentDateTime, PDO::PARAM_STR);

// Execute the query
$stmt->execute();

// Check if any rows match the current date/time
if ($stmt->rowCount() > 0) {
    // Fetch all matching records
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Event found that matches the current time - trigger the email for each row
    echo "Events found! Sending emails...\n";

    // Loop through each row and send an email
    foreach ($rows as $row) {
        $email = $row['email'];  // Make sure your table has a column 'email'
        $firstname = $row['firstname'];

        // Call send_email.php to send email to each recipient
        // Pass email and firstname to send_email.php
        include('send_email.php');
    }
} else {
    echo "No events scheduled for today.\n";
}
?>
