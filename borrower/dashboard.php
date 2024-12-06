<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Include your database connection file
include('db/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Get the current user's ID from the session
$userId = $_SESSION['user_id'];


// Fetch data from tblbooks with author, category names, and borrow/return status
$query = "SELECT b.bookId, b.title, b.quantity, 
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName,
                 COALESCE((
                     SELECT returned
                     FROM tblreturnborrow
                     WHERE tblreturnborrow.bookId = b.bookId
                       AND tblreturnborrow.borrowerId = ?
                     ORDER BY borrowId DESC
                     LIMIT 1
                 ), 'Yes') AS returnedStatus
          FROM tblbooks b
          JOIN tblauthor a ON b.authorId = a.authorId
          JOIN tblcategory c ON b.categoryId = c.categoryId";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books List</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .btn-action {
            padding: 5px 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-nostock {
            padding: 5px 10px;
            background-color: grey;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .return-btn {
            background-color: #28a745;
        }
        .return-btn:disabled {
            background-color: grey;
            cursor: not-allowed;
        }
        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Books List</h1>
        <p>Below is the list of all available books in the library.</p>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Available</th>
                    <th>Return</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Stocks</th>
                </tr>
            </thead>
            <tbody>
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

                        // Return button based on returnedStatus
                        echo "<td>";
                        if ($row['returnedStatus'] === 'No') {
                            echo "<button class='btn-action return-btn' onclick='handleReturn(" . $row['bookId'] . ")'>Return</button>";
                        } else {
                            echo "<button class='btn-action return-btn' disabled>Return</button>";
                        }
                        echo "</td>";

                        // Book details
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
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
        function handleBorrow(bookId) {
            if (confirm('Do you want to borrow this book?')) {
                const userId = <?= $_SESSION['user_id']; ?>;
                const username = "<?= $_SESSION['username']; ?>"; // Enclose in quotes for string
                $.ajax({
                    url: 'process/borrow-book.php',
                    type: 'POST',
                    data: { 
                        bookId: bookId, 
                        userId: userId,
                        username, username},
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function () {
                        alert('Error borrowing the book. Please try again.');
                    }
                });
            }
        }

        // Handle Return
        function handleReturn(bookId) {
            if (confirm('Do you want to return this book?')) {
                const userId = <?= $_SESSION['user_id']; ?>;
                const username = "<?= $_SESSION['username']; ?>"; // Enclose in quotes for string
                $.ajax({
                    url: 'process/return-book.php',
                    type: 'POST',
                    data: { 
                        bookId: bookId, 
                        userId: userId,
                        username, username},
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function () {
                        alert('Error returning the book. Please try again.');
                    }
                });
            }
        }
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
