<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

include('../process/db-connect.php');

// Get the borrowId from the URL
$borrowId = isset($_GET['borrowId']) ? intval($_GET['borrowId']) : 0;

// Ensure borrowId is provided
if ($borrowId === 0) {
    die("Invalid borrow ID.");
}

// Fetch full book and borrower details for the specific borrowId
$query = "
    SELECT 
        tblbooks.callNum,
        tblbooks.accessionNum,
        tblbooks.barcodeNum,
        tblreturnborrow.returnDate,
        tblreturnborrow.librarianName,
        tblreturnborrow.borrowedDate,
        CONCAT(tblborrowers.firstName, ' ', tblborrowers.surName) AS borrowerName
    FROM 
        tblreturnborrow
    INNER JOIN 
        tblborrowers ON tblreturnborrow.borrowerId = tblborrowers.idNumber
    INNER JOIN 
        tblbooks ON tblreturnborrow.bookId = tblbooks.bookId
    WHERE 
        tblreturnborrow.borrowId = ?";
$detailsStmt = $conn->prepare($query);
$detailsStmt->bind_param("i", $borrowId);
$detailsStmt->execute();
$detailsResult = $detailsStmt->get_result();

if ($detailsResult->num_rows > 0) {
    $details = $detailsResult->fetch_assoc();
    $dateBorrowed = $details['borrowedDate'];
    $borrowerName = $details['borrowerName'];
    $librarianName = $details['librarianName'];

} else {
    die("No records found for the specified borrow ID.");
}


// Fetch book details using the borrowId
$bookQuery = "
    SELECT 
        tblbooks.accessionNum,
        tblbooks.callNum,
        tblbooks.barcodeNum,
        tblbooks.title, -- Fetch the book title
        CONCAT(tblauthor.firstName, ' ', tblauthor.lastName) AS authorName -- Combine author's first and last name
    FROM 
        tblbooks
    INNER JOIN 
        tblreturnborrow ON tblreturnborrow.bookId = tblbooks.bookId
    INNER JOIN
        tblauthor ON tblbooks.authorId = tblauthor.authorId
    WHERE 
        tblreturnborrow.borrowId = ?";
$bookStmt = $conn->prepare($bookQuery);
$bookStmt->bind_param("i", $borrowId);
$bookStmt->execute();
$bookResult = $bookStmt->get_result();

if ($bookResult->num_rows > 0) {
    $bookInfo = $bookResult->fetch_assoc();
    $accessionNum = $bookInfo['accessionNum'];
    $callNum = $bookInfo['callNum'];
    $barcodeNum = $bookInfo['barcodeNum'];
    $title = $bookInfo['title'];
    $authorName = $bookInfo['authorName'];
} else {
    die("Book information not found.");
}

$logo = '../pdf/ustp.png'; // Path to USTP logo
$fm = '../pdf/bc.png'; // Path to the USTP logo
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

        .details div {
            width: 45%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
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
            background-color: grey; /* Different color for Go Back button */
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
    <h4>BOOK CARD</h4>
        <div class="details">
            <div>
                    <p><b>Call Number:</b> <?php echo $callNum; ?></p>
                    <p><b>Author:</b> <?php echo $authorName; ?></p>
                    <p><b>Title:</b> <?php echo $title; ?></p>

                    <div style="display: flex; justify-content: space-between; gap: 200px; align-items: center;">
                        <div style="white-space: nowrap;">
                        <b>Accession No.:</b> <?php echo $accessionNum; ?>
                    </div>
                    <div style="white-space: nowrap;">
                        <b>Barcode No.:</b> <?php echo $barcodeNum; ?>
                    </div>
                </div>
            </div>
        </div>


        <table class="table">
            <thead>
                <tr>
                    <th>Date Borrowed</th>
                    <th>Name of Borrower</th>
                    <th>Borrower's Signature</th>
                    <th>Librarian<br>Name & Signature</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo date("m-d-y", strtotime($dateBorrowed)); ?></td>
                    <td><?php echo htmlspecialchars($borrowerName); ?></td>
                    <td><?php echo '' ?></td>
                    <td><?php echo htmlspecialchars($librarianName); ?></td>
                </tr>
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
            width: 80%;
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

        .details div {
            width: 45%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
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
            background-color: grey; /* Different color for Go Back button */
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
    <h4>BOOK CARD</h4>
                        <div class="details">
                            <div>
                                <p><b>Call Number:</b> <?php echo $callNum; ?></p>
                                <p><b>Author:</b> <?php echo $authorName; ?></p>
                                <p><b>Title:</b> <?php echo $title; ?></p>

                                <div style="display: flex; justify-content: space-between; gap: 200px; align-items: center;">
                                    <div style="white-space: nowrap;">
                                        <b>Accession No.:</b> <?php echo $accessionNum; ?>
                                    </div>
                                    <div style="white-space: nowrap;">
                                        <b>Barcode No.:</b> <?php echo $barcodeNum; ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date Borrowed</th>
                                    <th>Name of Borrower</th>
                                    <th>Borrower's Signature</th>
                                    <th>Librarian Staff In-Charge<br>Name & Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo date("m-d-y", strtotime($dateBorrowed)); ?></td>
                                    <td><?php echo htmlspecialchars($borrowerName); ?></td>
                                    <td><?php echo '' ?></td>
                                    <td><?php echo htmlspecialchars($librarianName); ?></td>
                                </tr>
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
