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
            background-color: #f0f8ff; /* Light blue background */
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #ffffff; /* White card */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            color: #0056b3; /* Blue label */
            margin-bottom: 8px;
            text-align: left;
        }

        .form-group input {
            width: 95%;
            padding: 10px;
            border: 1px solid #d1e7ff;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #0056b3; /* Focus blue border */
            box-shadow: 0 0 4px rgba(0, 86, 179, 0.4);
        }

        .button-group {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .button-group button {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .print-button {
            background-color: #0056b3; /* Primary blue */
            color: #fff;
        }

        .print-button:hover {
            background-color: #003d80;
            transform: scale(1.05);
        }

        .back-button {
            background-color: #d1e7ff; /* Light blue */
            color: #0056b3;
        }

        .back-button:hover {
            background-color: #b3d7ff;
            transform: scale(1.05);
        }
        </style>
</head>
<body>

    <div class="form-container">
    <div class="form-group">
        <label for="librarian">Librarian Name:</label>
        <input 
        type="text" 
        id="librarian" 
        placeholder="Enter Librarian's Name" 
        value="" 
        aria-label="Librarian's Name">
    </div>

    <div class="button-group">
        <button 
        type="button" 
        class="print-button" 
        onclick="triggerPrint()">Print Borrower's Card</button>

        <button 
        type="button" 
        class="back-button" 
        onclick="goBack()">Go Back</button>
    </div>
    </div>

    <script>
        function triggerPrint() {
            // Get the librarian's name from the input
            const librarian = document.getElementById('librarian').value;

            // Open a new window and write content to it
            const w = window.open();
            w.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Borrower's Card</title>
                    <link rel="stylesheet" href="borrower-card.css">
                </head>
                <body>
                    <div class="card">
                        <div class="header">
                            <div class="logo">
                                <img src="<?php echo $logo ?>" alt="USTP Logo">
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
                                <p><b>Name:</b> <?php echo $name ?></p>
                                <p><b>Course/Year:</b> <?php echo $courseYear ?></p>
                                <p><b>Address:</b> <?php echo $address ?></p>
                                <p><b>Signature:</b> ___________________________</p>
                            </div>
                            <div class="photo">
                                <img src="<?php echo $photo ?>" alt="Profile Photo">
                            </div>
                        </div>
                        <div class="footer" style="text-align: center">
                            <p><b>Issued by:</b> ${librarian}</p>
                            <p><b>LIBRARIAN</b></p>
                        </div>
                    </div>
                </body>
                </html>
            `);

            // Trigger the print dialog
            w.document.close(); // Close the document stream
            w.print(); // Open the print dialog in the new window

            
            w.onafterprint = function () {
                w.close();
            };
        }

        function goBack() {
            // Redirect to the specified URL
            window.history.back();
        }
    </script>

</body>
</html>
