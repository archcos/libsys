<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

include('../process/db-connect.php');

// Get the referenceId from the URL
$referenceId = isset($_GET['referenceId']) ? intval($_GET['referenceId']) : 1;

// Fetch reference details from tblreference
$query = "
    SELECT 
        r.type, 
        r.author, 
        r.borrowerId, 
        r.title, 
        r.category, 
        r.date,
        b.firstName, 
        b.surName
    FROM 
        tblreference r
    INNER JOIN 
        tblborrowers b ON r.borrowerId = b.idNumber
    WHERE 
        r.referenceId = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $referenceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $record = $result->fetch_assoc();
    $type = $record['type'];
    $author = $record['author'];
    $title = $record['title'];
    $category = $record['category'];
    $date = $record['date'];
    $borrowerName = $record['firstName'] . ' ' . $record['surName'];
} else {
    die("Reference record not found.");
}

$logo = '../pdf/ustp.png'; // Path to the USTP logo
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reference Assistance Slip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            border: 2px solid black;
            background-color: #ffffff;
            position: relative;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .logo img {
            max-width: 80px;
        }

        .document-code {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .form-section {
            display: flex;
            align-items: center;
        }

        .form-section .checkbox-group {
            display: flex;
            gap: 15px; /* Adjust spacing between checkboxes */
            align-items: center;
        }



        .form-section label {
            flex: 0 0 100px; /* Adjust label width as needed */
            font-weight: bold;
        }

        .form-section input {
            flex: 1;
            border: none;
            border-bottom: 1px solid black;
            background-color: transparent;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-container button {
            width: 48%;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .print-button {
            background-color: #0056b3;
            color: white;
        }

        .back-button {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="<?php echo $logo; ?>" alt="USTP Logo">
        </div>
        <div class="document-code">
            Document Code: FM-USTP-LIB-02<br>
            Rev. No: 01 | Effective Date: 09.01.23 | Page: 4 of 4
        </div>

        <div class="header">
            <h3>UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES</h3>
            <p>Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</p>
            <h2>REFERENCE ASSISTANCE SLIP</h2>
        </div>

        <div class="form-section">
            <label>What type of library materials do you need?</label>
            <div class="checkbox-group">
                <input type="checkbox" <?php echo ($type == 'Book') ? 'checked' : ''; ?> disabled> Book
                <input type="checkbox" <?php echo ($type == 'Periodicals') ? 'checked' : ''; ?> disabled> Periodicals
                <input type="checkbox" <?php echo ($type == 'Thesis/Dissertation') ? 'checked' : ''; ?> disabled> Thesis/Dissertation
                <input type="checkbox" <?php echo ($type == 'Others') ? 'checked' : ''; ?> disabled> Others (Specify)
            </div>
        </div>

        <div class="form-section">
            <label>Author:</label>
            <input type="text" value="<?php echo htmlspecialchars($author); ?>" readonly>
        </div>

        <div class="form-section">
            <label>Title:</label>
            <input type="text" value="<?php echo htmlspecialchars($title); ?>" readonly>
        </div>

        <div class="form-section">
            <label>Subject/Topic:</label>
            <input type="text" value="<?php echo htmlspecialchars($category); ?>" readonly>
        </div>

        <div class="form-section" style="display: flex; align-items: center;">
            <label style="margin-right: 5px;">CALL NUMBER:</label>
            <input type="text" value="" readonly style="width: 100px;">
            
            <label style="margin-right: 5px;">SUBLOCATION:</label>
            <input type="text" value="" readonly style="width: 150px;">
        </div>

        <div style="display: flex; justify-content: space-between; gap: 20px;">

            <!-- Group 1 -->
            <div style="flex: 1;">
                <div class="form-section">
                    <label>Library User:</label>
                </div>

                <div class="form-section">
                    <div class="input-wrapper" style="display: block; width: 40%; margin-top: 5px;">
                        <input type="text" value="<?php echo htmlspecialchars($borrowerName); ?>" readonly style="display: block; width: 100%;">
                    </div>
                </div>
                <div class="form-section">
                    <p>
                        <span>Signature Over-Printed Name</span>
                    </p>
                </div>
            </div>

            <!-- Group 2 -->
            <div style="flex: 1;">
                <div class="form-section">
                    <label>Library Staff In-Charge</label>
                </div>

                <div class="form-section">
                    <div class="input-wrapper" style="display: block; width: 40%; margin-top: 5px;">
                        <input type="text" value="<?php echo htmlspecialchars($borrowerName); ?>" readonly style="display: block; width: 100%;">
                    </div>
                </div>
                <div class="form-section">
                    <p>
                        <span>Signature Over-Printed Name</span>
                    </p>
                </div>
            </div>

        </div>


        <div class="form-section" style="display: flex; ">
            <label>College/Department:</label>
            <input type="text" value="" readonly>

            <label>Date:</label>
            <input type="text" value="<?php echo htmlspecialchars($date); ?>" readonly>
        </div>


        <div class="button-container">
            <button class="print-button" onclick="window.print()">Print</button>
            <button class="back-button" onclick="window.history.back()">Go Back</button>
        </div>
    </div>
</body>
</html>
