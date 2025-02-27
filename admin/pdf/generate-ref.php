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
        CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
        r.borrowerId, 
        bs.title AS bookTitle, 
        r.category, 
        r.date,
        r.callNumber,
        r.subLocation,
        b.firstName, 
        b.surName
    FROM 
        tblreference r
    INNER JOIN 
        tblborrowers b ON r.borrowerId = b.idNumber
    INNER JOIN
        tblauthor a ON r.author = a.authorId
    INNER JOIN
        tblbooks bs ON r.title = bs.bookId
    WHERE 
        r.referenceId = ?";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $referenceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $record = $result->fetch_assoc();
    $type = $record['type'];
    $author = $record['authorName'];
    $title = $record['bookTitle'];
    $category = $record['category'];
    $date = $record['date'];
    $callNumber = $record['callNumber'];
    $subLocation = $record['subLocation'];
    $borrowerName = $record['firstName'] . ' ' . $record['surName'];
} else {
    die("Reference record not found.");
}

$logo = '../pdf/ustp.png'; // Path to the USTP logo
$fm = '../pdf/fmm.png'; // Path to the USTP logo

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
/* 
        .header {
            text-align: center;
            font-weight: bold;
        } */

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

        .header p {
            font-size: 10px; /* Adjust size as needed */
        }


        .form-section {
            display: flex;
            align-items: center;
        }

        .form-section .checkbox-group {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .form-section label {
            flex: 0 0 100px;
            font-weight: normal;
        }

        .form-section input {
            flex: 1;
            border: none;
            border-bottom: 1px solid black;
            background-color: transparent;
        }

        .form-box {
            border: 2px solid black;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

    </style>
    <script>
        window.onload = function () {
            window.print();
        };

        window.onafterprint = function () {
            window.history.back();
        };
    </script>
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
                <h2>REFERENCE ASSISTANCE SLIP</h2>
            </div>

            <div class="logo">
                <img src="<?php echo $fm; ?>" alt="USTP Logo">
            </div>
        </div>

        <div class="form-box">


        <div class="form-section">
            <span><label>What type of library materials do you need?</label></span>
        </div>


        <div class="form-section">
            <label>Please Check: </label>
            <div class="checkbox-group">
                <input type="checkbox" <?php echo ($type == 'Book') ? 'checked' : ''; ?> disabled> Book
                <input type="checkbox" <?php echo ($type == 'Periodicals') ? 'checked' : ''; ?> disabled> Periodicals
                <input type="checkbox" <?php echo ($type == 'Thesis/Dissertation') ? 'checked' : ''; ?> disabled> Thesis/Dissertation
                <input type="checkbox" <?php echo (!in_array($type, ['Book', 'Periodicals', 'Thesis/Dissertation'])) ? 'checked' : ''; ?> disabled> 
                <?php 
                    echo (!in_array($type, ['Book', 'Periodicals', 'Thesis/Dissertation'])) ? "Others ($type)" : "Others (Pls. specify)";
                ?>
            </div>
        </div>
        <div style="margin-top: 10px;">
    </div>  

        <div class="form-section">
            <label>AUTHOR:</label>
            <input type="text" value="<?php echo htmlspecialchars($author); ?>" readonly>
        </div>
        <div style="margin-top: 20px;">
    </div>

        <div class="form-section">
            <label>TITLE:</label>
            <input type="text" value="<?php echo htmlspecialchars($title); ?>" readonly>
        </div>
        <div style="margin-top: 20px;">
    </div>

        <div class="form-section">
            <label>SUBJECT/TOPIC:</label>
            <input type="text" value="<?php echo htmlspecialchars($category); ?>" readonly>
        </div>
        <div style="margin-top: 20px;">
    </div>

        <br>

        <div class="form-section">
            <label>CALL NUMBER:</label>
            <input type="text" style= "width: 20px" value="<?php echo htmlspecialchars($callNumber); ?>" readonly>
            <label>SUBLOCATION:</label>
            <input type="text" style= "width: 20px" value="<?php echo htmlspecialchars($subLocation); ?>" readonly>
        </div>
        <div style="margin-top: 20px;">
    </div>

        <br>


        <div style="display: flex; justify-content: space-between; gap: 20px;">
            <div style="flex: 1;">
                <div class="form-section">
                    <label>LIBRARY USER</label>
                </div>
                <div style="margin-top: 10px;">
    </div>
                <div class="form-section">
                    <input type="text" value="<?php echo htmlspecialchars($borrowerName); ?>" readonly>
                </div>
                <div class="form-section">
                    <p>Signature Over-Printed Name</p>
                </div>
                <div style="margin-top: 10px;">
    </div>
            </div>

            <div style="flex: 1;">
                <div class="form-section">
                    <span><label>LIBRARY STAFF IN-CHARGE</label></span>
                </div>
                <div style="margin-top: 10px;">
    </div>
                <div class="form-section">
                    <input type="text" value="" readonly>
                </div>
                <div class="form-section">
                    <p>Signature Over-Printed Name</p>
                </div>
                <div style="margin-top: 10px;">
    </div>
            </div>
        </div>
        
            <div class="form-section">
                <label>College/Department:</label>
                <input type="text" value="" readonly>
                <label>Date:</label>
                <input type="text" style= "width: 20px" value="<?php echo htmlspecialchars($date); ?>" readonly>
            </div>
        </div>

    </div>
</body>
</html>