<?php

// Include your database connection file
include('db/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Fetch data from tblbooks with author, category names, and borrow status
$query = "SELECT b.bookId, b.title, b.quantity, 
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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
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
            color: #007bff;
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
        <h1>Books Kiosk</h1>
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
                            echo "<button class='btn-nostock' disabled>Unavailable</button>";
                        }
                        echo "</td>";

                        // Book details
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
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
            // Initialize DataTables
            $('#dataTable').DataTable();
        });

        // Handle Borrow
        let currentBookId = null; // Track the selected book ID

        // Open modal for borrow confirmation
        function handleBorrow(bookId) {
            // Check if user is logged in (You can implement your own check here)
            const isLoggedIn = false; // Set this flag based on your login state

            if (!isLoggedIn) {
                window.location.href = "login.php"; // Redirect to login if not logged in
                return;
            }

            // If logged in, proceed with borrow process (you can add more logic here)
            currentBookId = bookId;
            // Open modal for borrow confirmation or proceed with other logic
        }

        // Search functionality for books
        function searchBooks() {
            const query = document.getElementById('searchBox').value.toLowerCase();
            const rows = document.getElementById('bookList').getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                // Check title, author, and category columns for the query
                for (let j = 2; j < 5; j++) {
                    if (cells[j].textContent.toLowerCase().includes(query)) {
                        match = true;
                        break;
                    }
                }

                // Show or hide rows based on match
                if (match) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>

<?php
?>
