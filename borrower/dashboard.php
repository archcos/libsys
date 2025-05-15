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
$query = "SELECT b.bookId, b.title, b.quantity, b.publisher, b.publishedDate, b.edition,
                 COALESCE(CONCAT(a.firstName, ' ', a.lastName), 'No Author') AS authorName, 
                 COALESCE(c.categoryName, 'Uncategorized') AS categoryName, 
                 YEAR(b.publishedDate) AS publishedYear,
                 COALESCE(( 
                     SELECT returned
                     FROM tblreturnborrow
                     WHERE tblreturnborrow.bookId = b.bookId
                       AND tblreturnborrow.borrowerId = ? 
                     ORDER BY borrowId DESC 
                     LIMIT 1
                 ), 'Yes') AS returnedStatus
          FROM tblbooks b
          LEFT JOIN tblauthor a ON b.authorId = a.authorId
          LEFT JOIN tblcategory c ON b.categoryId = c.categoryId";

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

        /* Book Modal Styles */
        .book-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
        }

        .book-modal-content {
            position: relative;
            background: white;
            margin: 2% auto;
            padding: 0;
            width: 55%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: modalFade 0.3s ease-in-out;
        }

        .book-cover {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .book-cover h2 {
            color: white;
            margin: 0;
            font-weight: bold;
            text-align: left;
            font-size: 2em;
        }

        .book-cover p {
            color: white;
            opacity: 0.9;
            text-align: left;
            margin-left: 0;
            font-size: 1.2em;
            margin-top: 5px;
        }

        .book-details {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .book-detail-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 14px;
        }

        .close-modal {
            position: absolute;
            right: 15px;
            top: 15px;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: 0.3s;
        }

        .close-modal:hover {
            color: #ddd;
        }

        .book-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
            margin-top: 12px;
        }

        .status-available {
            background-color: #28a745;
            color: white;
        }

        .status-borrowed {
            background-color: #dc3545;
            color: white;
        }

        @keyframes modalFade {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .btn-view {
            padding: 4px 8px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }

        .btn-view:hover {
            background-color: #0056b3;
        }

        .dashboard-header {
            margin-bottom: 20px;
        }

        .title-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .btn-refresh {
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-refresh:hover {
            background-color: #218838;
        }

        .btn-refresh i {
            font-size: 14px;
        }

        @media screen and (max-width: 768px) {
            .title-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
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
        
        <div class="dashboard-header">
            <div class="title-section">
                <h1>Books List</h1>
                <button id="refreshButton" class="btn-refresh" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <p>Below is the list of all available books in the library.</p>
        </div>

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
                        echo "<td><em>" . htmlspecialchars($row['title']) . "</em> <button class='btn-view' onclick='showBookModal(" . json_encode($row) . ")'><i class='fas fa-eye'></i></button></td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>";
                        if ($row['authorName'] == 'No Author') {
                            echo "<em>" . htmlspecialchars($row['title']) . "</em>. (" . htmlspecialchars($row['publishedYear']) . "). " . htmlspecialchars($row['publisher']);
                        } else {
                            echo htmlspecialchars($row['authorName']) . " (" . htmlspecialchars($row['publishedYear']) . "). <em>" . htmlspecialchars($row['title']) . "</em>. " . htmlspecialchars($row['publisher']);
                        }
                        echo "</td>";
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

    <!-- Book Modal -->
    <div id="bookModal" class="book-modal">
        <div class="book-modal-content">
            <span class="close-modal" onclick="closeBookModal()">&times;</span>
            <div class="book-cover">
                <h2 id="modalTitle" style="font-size: 2em; color: white; text-align: left;"></h2>
                <p id="modalAuthor" class="mb-2" style="font-size: 1.2em; color: white; text-align: left;"></p>
                <div id="modalStatus" class="book-status"></div>
            </div>
            <div class="book-details">
                <div class="book-detail-item">
                    <i class="fas fa-info-circle"></i>
                    <strong>Book ID:</strong>
                    <span id="modalBookId"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-layer-group"></i>
                    <strong>Subject:</strong>
                    <span id="modalCategory"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-building"></i>
                    <strong>Publisher:</strong>
                    <span id="modalPublisher"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-calendar"></i>
                    <strong>Copyright:</strong>
                    <span id="modalPublishedDate"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-book"></i>
                    <strong>Available Copies:</strong>
                    <span id="modalQuantity"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-bookmark"></i>
                    <strong>Edition:</strong>
                    <span id="modalEdition"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-quote-right"></i>
                    <strong>APA Citation:</strong>
                    <span id="modalAPA"></span>
                </div>
            </div>
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

        function showBookModal(bookData) {
            document.getElementById('modalTitle').textContent = bookData.title;
            document.getElementById('modalAuthor').textContent = 'by ' + bookData.authorName;
            document.getElementById('modalBookId').textContent = bookData.bookId;
            document.getElementById('modalCategory').textContent = bookData.categoryName;
            document.getElementById('modalPublisher').textContent = bookData.publisher;
            document.getElementById('modalPublishedDate').textContent = bookData.publishedYear;
            document.getElementById('modalQuantity').textContent = bookData.quantity;
            document.getElementById('modalEdition').textContent = bookData.edition || 'N/A';
            
            // Set APA citation
            let apaCitation = '';
            if (bookData.authorName === 'No Author') {
                apaCitation = `<em>${bookData.title}</em>. (${bookData.publishedYear}). ${bookData.publisher}`;
            } else {
                apaCitation = `${bookData.authorName} (${bookData.publishedYear}). <em>${bookData.title}</em>. ${bookData.publisher}`;
            }
            document.getElementById('modalAPA').innerHTML = apaCitation;

            // Set status
            const statusElement = document.getElementById('modalStatus');
            if (bookData.quantity > 0) {
                statusElement.textContent = 'Available';
                statusElement.className = 'book-status status-available';
            } else {
                statusElement.textContent = 'Not Available';
                statusElement.className = 'book-status status-borrowed';
            }

            document.getElementById('bookModal').style.display = 'block';
        }

        function closeBookModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function refreshDashboard() {
            location.reload();
        }
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
