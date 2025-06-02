<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

include('../process/db-connect.php');

$borrowerType = isset($_GET['borrowerType']) ? $_GET['borrowerType'] : '';
$remarks = isset($_GET['remarks']) ? $_GET['remarks'] : '';


// Fetch return records with borrower details
$query = "
    SELECT 
        tblborrowers.surName, 
        tblborrowers.firstName,
        tblborrowers.idNumber, 
        tblborrowers.timeReceived, 
        tblborrowers.dateRegistered, 
        tblborrowers.specialInstructions,
        tblcourses.courseName
    FROM 
        tblborrowers
    INNER JOIN 
        tblcourses ON tblborrowers.course = tblcourses.courseId
    WHERE 
        tblborrowers.receipt = 'Yes'
";

// Add filters
$params = [];
$types = '';

if (!empty($borrowerType)) {
    $query .= " AND tblborrowers.borrowerType = ?";
    $params[] = $borrowerType;
    $types .= 's';
}

if (!empty($remarks)) {
    $query .= " AND tblborrowers.remarks = ?";
    $params[] = $remarks;
    $types .= 's';
}

// Prepare and execute the query
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();


$records = [];
while ($row = $result->fetch_assoc()) {
    $row['dateRegistered'] = date("m-d-Y", strtotime($row['dateRegistered']));
    $records[] = $row;
}



$logo = 'ustp.png';
$photo = 'image.png';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @media print {
    @page {
        size: auto;   /* auto is the initial value */
        margin: 0;  /* this affects the margin in the printer settings */
    }

    
    /* Hide URL and date */
    @page {
        margin: 0;
    }
    
    @page :left {
        @bottom-left {
            content: "";
        }
    }
    
    @page :right {
        @bottom-right {
            content: "";
        }
    }
    
    @page :left {
        @top-left {
            content: "";
        }
    }
    
    @page :right {
        @top-right {
            content: "";
        }
    }
}
        body { font-family: Arial, sans-serif; font-size: 12px; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid black; padding: 8px; text-align: center; }
        .table th { background-color: #f2f2f2; text-transform: uppercase; }
        .print-button { margin-top: 20px; padding: 10px 15px; font-size: 14px; cursor: pointer; }
        .logo {
            display: flex;
            justify-content: center; /* Centers the logo horizontally */
            align-items: center; /* Centers the logo vertically within its container */
            margin-top: 10px; /* Adjust the spacing from the top */
        }

        .logo img {
            max-width: 80px;
        }


    </style>
</head>
<body>


    <div class="logo">
            <img src="<?php echo $logo; ?>" alt="USTP Logo">
        </div>
        
        <div class="header">
            <h3>UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES</h3>
            <p>Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</p>
            <h2>LIBRARY CARD RECEIPT LOG</h2>

        </div>

    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>DATE</th>
                <th>NAME OF STUDENT/USER</th>
                <th>ID NUMBER</th>
                <th>PROGRAM/COURSE</th>
                <th>TIME OF RECEIPT</th>
                <th>RECEIVED BY (SIGNATURE)</th>
                <th>NOTES (E.G., SPECIAL INSTRUCTIONS)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['dateRegistered']); ?></td>
                <td><?php echo htmlspecialchars($record['surName'] . ', ' . $record['firstName']); ?></td>
                <td><?php echo htmlspecialchars($record['idNumber']); ?></td>
                <td><?php echo htmlspecialchars($record['courseName']); ?></td>
                <td><?php echo htmlspecialchars($record['timeReceived']); ?></td>
                <td></td> <!-- Blank -->
                <td><?php echo htmlspecialchars($record['specialInstructions']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();

            // Detect when the print dialog is closed
            window.onafterprint = function() {
                window.history.back(); // Go back to the previous page
            };
        };
    </script>
</body>
</html>
