<?php

// Include your database connection file
include('db/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Fetch data from tblbooks with author, category names, and borrow status
$query = "SELECT b.bookId, b.title, b.quantity, b.publisher, b.publishedDate,
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName
          FROM tblbooks b
          JOIN tblauthor a ON b.authorId = a.authorId
          JOIN tblcategory c ON b.categoryId = c.categoryId";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Kiosk</title>

    <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link id="main-css-href" rel="stylesheet" href="assets/css/style.css" />
  <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
   <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
    font-family: 'Roboto', sans-serif;
    background: url('assets/img/lib.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    color: #333;
}


        .header {
            display: flex;
            justify-content: center; /* Center the header content */
            align-items: center;
            padding: 20px;
            position: relative; /* Allows positioning of the buttons */
            color: white;
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            text-align: center;
        }

        .header .btn-back {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .header .btn-back:hover {
            background-color: #5a6268;
        }

        .header .btn-login {
            position: absolute; /* Position the login button independently */
            right: 20px; /* Align it to the right side */
            top: 50%; /* Center vertically within the header */
            transform: translateY(-50%);
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .header .btn-login:hover {
            background-color: rgb(6, 57, 112);
            color: white;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: black;
            font-size: 2rem;
        }

        p {
            text-align: center;
            font-size: 1.1rem;
            color: #555;
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            padding: 12px 15px;
            font-size: 16px;
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 5px;
            border: 2px solid #007bff;
            outline: none;
            transition: border 0.3s;
        }

        .search-container input:focus {
            border-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            background-color: #fff;
        }

        .btn-action {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-action:hover {
            background-color: #0056b3;
        }

        .btn-nostock {
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            table {
                font-size: 0.9rem;
            }

            .search-container input {
                width: 100%;
            }
        }
        
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        <a href="Kiosk.php" class="btn-back">
        <i class="fas fa-arrow-left"></i>  </a>
            <h1>Library Kiosk</h1><br>
            <a href="login.php" class="btn-login">Login</a>
        </div>

        <p>Explore the list of books available for borrowing in the library. Search, select, and borrow your favorite books.</p>

        <!-- Search Box -->
        <div class="search-container">
            <input type="text" id="searchBox" placeholder="Search books by title, author, or category..." onkeyup="searchBooks()">
        </div>

        <!-- Books Table -->
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Available</th>
                    <th>Stocks</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Publisher</th>
                    <th>Published Date</th>
                </tr>
            </thead>
            <tbody id="bookList">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        
                        // Borrow button or unavailable
                        echo "<td>";
                        if ($row['quantity'] > 0) {
                            echo "<button class='btn-action' onclick='handleBorrow(" . $row['bookId'] . ")'>Borrow</button>";
                        } else {
                            echo "<button class='btn-nostock btn-light' disabled>Unavailable</button>";
                        }
                        echo "</td>";

                        // Book details
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['publisher']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['publishedDate']) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        function handleBorrow(bookId) {
            const isLoggedIn = false;
            if (!isLoggedIn) {
                window.location.href = "login.php";
                return;
            }
            currentBookId = bookId;
        }

        function searchBooks() {
            const query = document.getElementById('searchBox').value.toLowerCase();
            const rows = document.getElementById('bookList').getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = rows[i].innerText.toLowerCase().includes(query) ? '' : 'none';
            }
        }
    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
?>
