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

// Check if the user has unpaid penalties
$penaltyCheckQuery = "
    SELECT COUNT(*) AS unpaidPenalties
    FROM tblpenalties
    WHERE borrowerId = ? AND paid = 'No'
";
$penaltyCheckStmt = $conn->prepare($penaltyCheckQuery);
$penaltyCheckStmt->bind_param("i", $userId);
$penaltyCheckStmt->execute();
$penaltyCheckResult = $penaltyCheckStmt->get_result();
$penaltyCheckRow = $penaltyCheckResult->fetch_assoc();
$unpaidPenalties = $penaltyCheckRow['unpaidPenalties'] > 0;

// Fetch data from tblbooks with author, category names, and borrow/return status
$query = "SELECT b.bookId, b.title, b.quantity, 
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName, YEAR(b.publishedDate) AS publishedYear,
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
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        .btn-action {
            padding: 5px 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-borrowed {
            padding: 5px 10px;
            background-color: grey;
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
        .penalty-warning {
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($unpaidPenalties): ?>
            <div class="penalty-warning">
                You have unpaid penalties. <a href="list-penalties.php">Click here</a> to view your penalties.
            </div>
        <?php endif; ?>
        
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
                    <th>APA</th>
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
                        // Check if the user has already borrowed the book
                        $checkQuery = "SELECT * FROM tblreturnborrow WHERE borrowerId = ? AND bookId = ? AND returned = 'No' LIMIT 1";
                        $checkStmt = $conn->prepare($checkQuery);
                        $checkStmt->bind_param("ii", $userId, $row['bookId']);  // Bind borrowerId and bookId
                        $checkStmt->execute();
                        $checkResult = $checkStmt->get_result();
                        
                        if ($row['quantity'] > 0) {
                            if ($checkResult->num_rows > 0) {
                                // If the user has already borrowed a book, show "Borrowed"
                                echo "<button class='btn-borrowed' disabled>Borrowed</button>";
                            } else {
                                // Otherwise, show "Borrow" button
                                echo "<button class='btn-action' onclick='handleBorrow(" . $row['bookId'] . ")'>Borrow</button>";
                            }
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
                        echo "<td>" . " (" . htmlspecialchars($row['authorName']) . " , " .  htmlspecialchars($row['publishedYear']) . ")" . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

        <!-- Borrow Modal -->
        <div id="borrowModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="background:white; margin:10% auto; padding:20px; width:300px; border-radius:10px; text-align:center;">
            <h2>Confirm Borrow</h2>
            <p>Press confirm to borrow the book</p>
            <br>
            <button id="confirmBorrowBtn" style="background:blue; color:white; padding:5px 15px; border:none; border-radius:5px; cursor:pointer;">Confirm</button>
            <button onclick="closeModal()" style="background:grey; color:white; padding:5px 15px; border:none; border-radius:5px; cursor:pointer;">Cancel</button>
        </div>
    </div>

    <!-- Return Modal -->
    <div id="returnModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="background:white; margin:10% auto; padding:20px; width:300px; border-radius:10px; text-align:center;">
            <h2>Confirm Return</h2>
            <p>Press confirm to return the book</p>
            <br>
            <button id="confirmReturnBtn" style="background:green; color:white; padding:5px 15px; border:none; border-radius:5px; cursor:pointer;">Confirm</button>
            <button onclick="closeReturnModal()" style="background:grey; color:white; padding:5px 15px; border:none; border-radius:5px; cursor:pointer;">Cancel</button>
        </div>
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
            currentBookId = bookId;
            document.getElementById('borrowModal').style.display = 'block';
        }

        // Close the modal
        function closeModal() {
            document.getElementById('borrowModal').style.display = 'none';
            document.getElementById('userIdInput').value = '';
        }

        // Approve borrow and send AJAX request
        document.getElementById('confirmBorrowBtn').addEventListener('click', function() {
            const userId = "<?= $_SESSION['user_id']; ?>";
            const username = "<?= $_SESSION['username']; ?>";

            $.ajax({
                url: 'process/borrow-book.php',
                type: 'POST',
                data: { 
                    bookId: currentBookId, 
                    userId: userId, 
                    username: username 
                },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Reload page to update UI
                },
                error: function() {
                    alert('Error processing borrow request. Please try again.');
                }
            });

            closeModal(); // Close modal after request
        });

        let currentBookIdReturn = null; // Track the selected book ID for return

        // Open modal for return confirmation
        function handleReturn(bookId) {
            currentBookIdReturn = bookId;
            document.getElementById('returnModal').style.display = 'block';
        }

        // Close the return modal
        function closeReturnModal() {
            document.getElementById('returnModal').style.display = 'none';
            document.getElementById('userIdInputReturn').value = '';
        }

        // Approve return and send AJAX request
        document.getElementById('confirmReturnBtn').addEventListener('click', function() {
            const userId = "<?= $_SESSION['user_id']; ?>";
            const username = "<?= $_SESSION['username']; ?>";

            $.ajax({
                url: 'process/return-book.php',
                type: 'POST',
                data: { 
                    bookId: currentBookIdReturn, 
                    userId: userId, 
                    username: username 
                },
                success: function(response) {
                    alert(response.message);
                    location.reload(); // Reload page to update UI
                },
                error: function() {
                    alert('Error processing return request. Please try again.');
                }
            });

            closeReturnModal(); // Close modal after request
        });
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
