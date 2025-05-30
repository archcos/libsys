<?php
session_start();
require_once '../templates/messages.php';
require_once '../templates/alert.php';
require_once 'db-connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Your existing validation and data processing code here
    
    try {
        // Your existing database insertion code here
        
        // If successful
        $_SESSION['alert_message'] = BOOK_ADDED;
        $_SESSION['alert_type'] = 'success';
        header('Location: ../add-book.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['alert_message'] = SYSTEM_ERROR;
        $_SESSION['alert_type'] = 'error';
        header('Location: ../add-book.php');
        exit();
    }
} else {
    $_SESSION['alert_message'] = INVALID_REQUEST;
    $_SESSION['alert_type'] = 'error';
    header('Location: ../add-book.php');
    exit();
}
?> 