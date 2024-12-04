<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php'); // Change 'login.php' to your login page
    exit; // Stop script execution after the redirect
}

// Include your database connection file
include('db/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Fetch data from tblbooks with author and category names
$query = "SELECT b.bookId, b.title, b.quantity, 
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName 
          FROM tblbooks b
          JOIN tblauthor a ON b.authorId = a.authorId
          JOIN tblcategory c ON b.categoryId = c.categoryId";
$result = $conn->query($query);
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
            background-color: blue; /* Green for active */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .btn-nostock {
            padding: 5px 10px;
            background-color: grey; /* Green for active */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .return-btn {
            background-color: #28a745;
        }
        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: darkred;
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

                        // Return button
                        echo "<td>";
                        echo "<button class='btn-action return-btn' onclick='handleReturn(" . $row['bookId'] . ")'>Return</button>";
                        echo "</td>";

                        // Book details
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";

        
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No books found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Borrow Modal -->
    <div id="borrowModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Borrow Book</h3>
            <form id="borrowForm">
                <input type="hidden" id="borrowModalBookId">
                <label for="borrowerId">ID Number:</label><br>
                <input type="number" id="borrowerId" required><br><br>
                <label for="librarianName">Librarian Full Name:</label><br>
                <input type="text" id="librarianName" required><br><br>
                <label for="returnDate">Return Date:</label><br>
                <input type="date" id="returnDate" required><br><br>
                <button type="submit" class="btn-action">Approve</button>
                <button type="button" onclick="closeModal('borrowModal')" class="cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Return Modal -->
    <div id="returnModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Return Book</h3>
            <form id="returnForm">
                <input type="hidden" id="returnModalBookId">
                <label for="returnerId">ID Number:</label><br>
                <input type="number" id="returnerId" required><br><br>
                <button type="submit" class="btn-action">Confirm Return</button>
                <button type="button" onclick="closeModal('returnModal')" class="cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
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
                    username, username
                },
                success: function (response) {
                    alert(response.message);
                    if (response.success) {
                        console.log(response);
                        location.reload();
                    }
                },
                error: function () {
                    alert('Error borrowing the book. Please try again.');
                },
            });
        }
    }


    // Handle Return
    function handleReturn(bookId) {
        if (confirm('Do you want to return this book?')) {
            const userId = <?= $_SESSION['user_id']; ?>; // Pass the user_id from the session
            const username = "<?= $_SESSION['username']; ?>"; // Enclose in quotes for string

            // Send AJAX request to handle returning
            $.ajax({
                url: 'process/return-book.php',
                type: 'POST',
                data: { 
                    bookId: bookId, 
                    userId: userId,
                    username, username
                },
                success: function (response) {
                    alert(response.message);
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function () {
                    alert('Error returning the book. Please try again.');
                },
            });
        }
    }

    
    $(document).ready(function() {
            // Initialize DataTables
            $('#dataTable').DataTable();

            // Handle delete button clic
        });
</script>

</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
