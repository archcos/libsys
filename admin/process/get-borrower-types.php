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
    // Query to get count of borrowers by type
    $query = "SELECT 
                borrowerType,
                COUNT(*) as count
              FROM tblborrowers 
              GROUP BY borrowerType 
              ORDER BY 
                CASE borrowerType
                    WHEN 'Student' THEN 1
                    WHEN 'Faculty' THEN 2
                    WHEN 'Staff' THEN 3
                    ELSE 4
                END";

    $result = $conn->query($query);

    $labels = [];
    $values = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['borrowerType'];
            $values[] = intval($row['count']);
        }
    }

    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close(); 