<?php
// db.php: Establish the database connection
$servername = "localhost";  // Your database server (localhost if running on your machine)
$username = "root";         // Your database username (default is "root" for XAMPP)
$password = "";             // Your database password (default is empty for XAMPP)
$dbname = "balibsysdb";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
