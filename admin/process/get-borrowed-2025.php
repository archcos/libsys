<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include('db-connect.php');

try {
    // Query to get monthly borrowed books count for 2025
    $query = "SELECT 
                MONTH(borrowedDate) as month,
                COUNT(*) as count
              FROM tblreturnborrow 
              WHERE YEAR(borrowedDate) = 2025
              GROUP BY MONTH(borrowedDate)
              ORDER BY month";

    $result = $conn->query($query);
    
    // Initialize array with zeros for all months
    $monthlyData = array_fill(0, 12, 0);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Adjust for 0-based array (month-1)
            $monthlyData[$row['month']-1] = intval($row['count']);
        }
    }

    echo json_encode([
        'monthlyData' => $monthlyData
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close(); 