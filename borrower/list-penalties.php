<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

include('db/db-connect.php'); // Database connection
ob_start();

// Get logged-in user's ID
$userId = $_SESSION['user_id']; 

// Fetch penalties based on user_id (Comparing borrowerId with userId)
$penaltyQuery = "
    SELECT 
        p.penaltyId, 
        p.bookId, 
        p.penalty, 
        p.cost, 
        p.paid, 
        b.title AS bookTitle 
    FROM tblpenalties p
    JOIN tblbooks b ON p.bookId = b.bookId
    WHERE p.borrowerId = ? 
    ORDER BY p.penaltyId DESC
";
$penaltyStmt = $conn->prepare($penaltyQuery);
$penaltyStmt->bind_param('i', $userId);
$penaltyStmt->execute();
$penaltyResult = $penaltyStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Penalties</title>
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">

    <!-- Add some basic styles -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        td.remarks {
            white-space: pre-line;
            word-break: break-word;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Penalties</h1>

        <!-- Table to display the penalties -->
        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Remarks</th>
                    <th>Cost</th>
                    <th>Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any penalties for the user
                if ($penaltyResult->num_rows > 0) {
                    // Loop through and display the penalties
                    while ($row = $penaltyResult->fetch_assoc()) {
                        // Format the penalty details
                        $bookTitle = htmlspecialchars($row['bookTitle']);
                        $penalty = htmlspecialchars($row['penalty']);
                        $cost = htmlspecialchars($row['cost']);
                        $paid = htmlspecialchars($row['paid']) == 'Yes' ? 'Paid' : 'Unpaid';

                        echo "<tr>";
                        echo "<td>" . $bookTitle . "</td>";
                        echo "<td class='remarks'>" . $penalty . "</td>";
                        echo "<td>" . $cost . "</td>";
                        echo "<td>" . $paid . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No penalties found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
