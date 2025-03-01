<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

include('../process/db-connect.php');

// Get the borrowId from the URL (since you mentioned that the primary key is borrowId)
$borrowId = isset($_GET['borrowId']) ? intval($_GET['borrowId']) : 1;

// Fetch the bookId using the borrowId from tblreturnborrow
$query = "
    SELECT bookId FROM tblreturnborrow WHERE borrowId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $borrowId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bookId = $result->fetch_assoc()['bookId'];
} else {
    die("Borrow record not found.");
}

// Fetch all return records related to this bookId
$query = "
    SELECT 
        tblreturnborrow.returnDate,
        tblreturnborrow.librarianName
    FROM 
        tblreturnborrow
    INNER JOIN 
        tblborrowers ON tblreturnborrow.borrowerId = tblborrowers.idNumber
    WHERE 
        tblreturnborrow.bookId = ?
    ORDER BY tblreturnborrow.returnDate DESC
    LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch all rows and store them
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
} else {
    $records = [];
    die("No return records found for this book.");
}

// Fetch book information (accession, call number, barcode) using the bookId
$bookQuery = "SELECT accessionNum, callNum, barcodeNum FROM tblbooks WHERE bookId = ?";
$bookStmt = $conn->prepare($bookQuery);
$bookStmt->bind_param("i", $bookId);
$bookStmt->execute();
$bookResult = $bookStmt->get_result();

if ($bookResult->num_rows > 0) {
    $bookInfo = $bookResult->fetch_assoc();
    $accessionNum = $bookInfo['accessionNum'];
    $callNum = $bookInfo['callNum'];
    $barcodeNum = $bookInfo['barcodeNum'];
} else {
    die("Book not found.");
}

$logo = '../pdf/ustp.png'; // Path to USTP logo
$fm = '../pdf/dds.png'; // Path to the USTP logo
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Return Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            width: 40%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid black;
            background-color: #ffffff;
            position: relative;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        

        .logo img {
            max-width: 80px;
        }

        .header {
            text-align: center;
            flex: 1; /* Allows the header to take up remaining space */
        }

        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 0 10px;
        }

        .details input {
            flex: 1;
            border: none;
            border-bottom: 1px solid black;
            background-color: transparent;
        }

        .details div {
            width: 45%;
        }

        .table {
    width: 90%; /* Make the table half-width */
    border-collapse: collapse;
    margin: 20px auto; /* Center the table */
    table-layout: fixed; /* Important: Fix the table layout */
}

.table th, .table td {
    border: 1px solid black;
    padding: 8px;
    text-align: center;
    width: 50%; /* Make each column take up 50% of the table width */
}

        .table th {
            background-color: #f2f2f2;
        }

        h4 {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #0056b3;
            color: #fff;
            font-size: 16px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #003d80;
            transform: scale(1.05);
        }
        .button-container {
            display: flex;
            justify-content: space-between; /* Space between buttons */
            gap: 10px; /* Optional: adds space between the buttons */
        }

        .button-container button {
            width: 48%; /* Adjust button width if necessary */
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .button-container button:hover {
            background-color: #003d80; /* Optional: Change button color on hover */
        }

        .back-button {
            background-color:grey; /* Different color for Go Back button */
            color: white;
        }

        button {
            background-color: #0056b3; /* Default color for Print button */
            color: white;
        }

    </style>
</head>
<body>
    <div class="container">
    <div class="header-container">
            <div class="logo">
                <img src="<?php echo $logo; ?>" alt="USTP Logo">
            </div>

            <div class="header">
                <h3>UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES</h3>
                <p>Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</p>
            
            </div>

            <div class="logo">
                <img src="<?php echo $fm; ?>" alt="USTP Logo">
            </div>
        </div>
        <div style="margin-top: 20px;">
    </div>

        <div class="details">
            <div>
                <b>Call No.:</b> <?php echo $callNum; ?><br>
                <b>Accession No.:</b> <?php echo $accessionNum; ?><br>
                <b>Barcode No.:</b> <?php echo $barcodeNum; ?>
            </div>
        </div>

        <h4>DATE DUE SLIP</h4>

        <table class="table">
            <thead>
                <tr>
                    <th>Return Date</th>
                    <th>Librarian Staff In-Charge<br>Name & Signature</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo date("m.d.y", strtotime($record['returnDate'])); ?></td>
                    <td><?php echo $record['librarianName']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="button-container">
            <button onclick="triggerPrint()">Print Date Due Slip</button>
            <button 
                type="button" 
                class="back-button" 
                onclick="goBack()">Go Back</button>
        </div>

    </div>

    <script>
        function triggerPrint() {
            const w = window.open();
            w.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Book Return Card</title>
                    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            width: 70%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid black;
            background-color: #ffffff;
            position: relative;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        

        .logo img {
            max-width: 80px;
        }

        .header {
            text-align: center;
            flex: 1; /* Allows the header to take up remaining space */
        }

        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 0 10px;
        }

        .details input {
            flex: 1;
            border: none;
            border-bottom: 1px solid black;
            background-color: transparent;
        }

        .details div {
            width: 45%;
        }

        .table {
    width: 90%; /* Make the table half-width */
    border-collapse: collapse;
    margin: 20px auto; /* Center the table */
    table-layout: fixed; /* Important: Fix the table layout */
}

.table th, .table td {
    border: 1px solid black;
    padding: 8px;
    text-align: center;
    width: 50%; /* Make each column take up 50% of the table width */
}

        .table th {
            background-color: #f2f2f2;
        }

        h4 {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #0056b3;
            color: #fff;
            font-size: 16px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #003d80;
            transform: scale(1.05);
        }
        .button-container {
            display: flex;
            justify-content: space-between; /* Space between buttons */
            gap: 10px; /* Optional: adds space between the buttons */
        }

        .button-container button {
            width: 48%; /* Adjust button width if necessary */
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .button-container button:hover {
            background-color: #003d80; /* Optional: Change button color on hover */
        }

        .back-button {
            background-color:grey; /* Different color for Go Back button */
            color: white;
        }

        button {
            background-color: #0056b3; /* Default color for Print button */
            color: white;
        }

    </style>
                </head>
                <body>
                   <div class="container">
    <div class="header-container">
            <div class="logo">
                <img src="<?php echo $logo; ?>" alt="USTP Logo">
            </div>

            <div class="header">
                <h3>UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES</h3>
                <p>Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</p>
            
            </div>

            <div class="logo">
                <img src="<?php echo $fm; ?>" alt="USTP Logo">
            </div>
        </div>
        <div style="margin-top: 20px;">
    </div>

                        <div class="details">
                            <div>
                                <b>Call No.:</b> <?php echo $callNum; ?><br>
                                <b>Accession No.:</b> <?php echo $accessionNum; ?><br>
                                <b>Barcode No.:</b> <?php echo $barcodeNum; ?>
                            </div>
                        </div>
 <h4>DATE DUE SLIP</h4>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Return Date</th>
                                    <th>Librarian Staff In-Charge<br>Name & Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo date("m-d-y", strtotime($record['returnDate'])); ?></td>
                                    <td><?php echo $record['librarianName']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </body>
                </html>
            `);
            w.document.close(); // Close the document stream
            w.print(); // Open the print dialog in the new window

            w.onafterprint = function () {
                w.close();
            };
        }

        function goBack() {
            // Redirect to the previous page
            window.history.back();
        }
    </script>
</body>
</html>
