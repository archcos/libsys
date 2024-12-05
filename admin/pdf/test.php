<?php
include('../process/db-connect.php'); // Adjust path if needed

// Fetch borrower details from the database
$idNumber = isset($_GET['idNumber']) ? intval($_GET['idNumber']) : 214;
$query = "SELECT firstName, surName, course, year, homeAddress FROM tblborrowers WHERE idNumber = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['surName'] . ', ' . $row['firstName']; // Combine first and last name
    $courseYear = $row['course'] . ', ' . $row['year']; // Combine course and year
    $address = $row['homeAddress']; // Borrower's address
} else {
    die("Borrower not found.");
}


// Librarian's name (static)
$librarian = "KAREN ROSE A. ONTOLAN, RL";
$logo = 'ustp.png';
$photo = 'image.png';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Borrower's Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .content {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .print-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .content, .content * {
                visibility: visible;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button class="print-button" onclick="triggerPrint()">Print Borrower's Card</button>

    <script>
        var w;
        
        function triggerPrint() {
            // Open a new window and write content to it
            w = window.open();
            w.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Borrower\'s Card</title>
                    <link rel="stylesheet" href="borrower-card.css">
                </head>
                <body>
                    <div class="card">
                        <div class="header">
                            <div class="logo">
                                <img src="<?php echo $logo ?> " alt="USTP Logo">
                            </div>
                            <div class="header-text">
                                <h2>UNIVERSITY OF SCIENCE & TECHNOLOGY OF SOUTHERN PHILIPPINES</h2>
                                <h3>Balubal, Cagayan De Oro City</h3>
                                <h4>LIBRARY DEPARTMENT</h4>
                                <h5>Library Borrower’s Card/Circulation Section</h5>
                            </div>
                        </div>
                        <div class="content">
                            <div class="details">
                                <p><b>Name:</b> <?php echo $name ?> </p>
                                <p><b>Course/Year:</b>  <?php echo $courseYear ?></p>
                                <p><b>Address:</b>  <?php echo $address ?></p>
                                <p><b>Signature:</b> ___________________________</p>
                            </div>
                            <div class="photo">
                                <img src="<?php echo $photo ?> " alt="Profile Photo">
                            </div>
                        </div>
                        <div class="footer" style="text-align: center">
                            <p><b>Issued by:</b> <?php echo $librarian ?></p>
                            <p><b>LIBRARIAN</b></p>
                        </div>
                    </div>
                </body>
                </html>
            `);

            // Once the content is written, trigger the print dialog
            w.document.close();  // Close the document stream
            w.print();  // Open the print dialog in the new window
        }
    </script>

</body>
</html>
