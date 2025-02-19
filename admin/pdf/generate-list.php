<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: ../sign-in.php');
    exit;
}

include('../process/db-connect.php');

$logo = '../pdf/ustp.png'; // Path to the USTP logo
$fm = '../pdf/fm.png'; // Path to another logo

// Fetch borrower data
$query = "
    SELECT 
        b.dateRegistered, 
        b.libraryId, 
        b.idNumber, 
        b.surName, 
        b.firstName, 
        LEFT(b.middleName, 1) AS middleInitial, 
        c.courseName, 
        b.gender, 
        b.birthDate, 
        b.homeAddress,
        b.remarks
    FROM 
        tblborrowers b
    LEFT JOIN 
        tblcourses c ON b.course = c.courseId
    ORDER BY 
        b.dateRegistered DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            width: 90%;
            margin: 30px auto;
            padding: 20px;
            border: 2px solid black;
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
            flex: 1;
        }

        .header p {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
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
                <h2>LIBRARY REGISTRATION FORM</h2>
                <h4>____ Semester SY: _____________ </h4>

            </div>

            <div class="logo">
                <img src="<?php echo $fm; ?>" alt="USTP Logo">
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. </th>
                    <th>Date of Registration</th>
                    <th>ID Number</th>
                    <th>Surname</th>
                    <th>First Name</th>
                    <th>Middle Initial</th>
                    <th>Course</th>
                    <th>Gender</th>
                    <th>Birth Date</th>
                    <th>Home Address</th>
                    <th>Signature</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['libraryId']}</td>
                                <td>{$row['dateRegistered']}</td>
                                <td>{$row['idNumber']}</td>
                                <td>{$row['surName']}</td>
                                <td>{$row['firstName']}</td>
                                <td>{$row['middleInitial']}.</td>
                                <td>{$row['courseName']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['birthDate']}</td>
                                <td>{$row['homeAddress']}</td>
                                <td></td> <!-- Empty Signature Column -->
                                <td>{$row['remarks']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
