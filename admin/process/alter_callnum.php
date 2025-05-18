<?php
include('db-connect.php');

// Alter the callNum field to allow longer values
$sql = "ALTER TABLE tblbooks MODIFY COLUMN callNum VARCHAR(50)";

if ($conn->query($sql) === TRUE) {
    echo "Call Number field updated successfully to allow longer values.";
} else {
    echo "Error updating Call Number field: " . $conn->error;
}

$conn->close();
?> 