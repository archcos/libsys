<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

include('process/db-connect.php'); // Database connection
ob_start();

// Get filter parameters
$categoryId = isset($_GET['categoryId']) ? intval($_GET['categoryId']) : null;
$authorId = isset($_GET['authorId']) ? intval($_GET['authorId']) : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

// Prepare base query
$query = "
    SELECT 
        b.bookId, b.title, b.quantity, 
        COALESCE(CONCAT(a.firstName, ' ', a.lastName), 'No Author') AS authorName, 
        COALESCE(c.categoryName, 'No Subject') AS categoryName, 
        b.publisher, b.publishedDate, YEAR(b.publishedDate) AS publishedYear,
        b.edition, b.accessionNum, b.barcodeNum, b.callNum, b.status,
        CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM tblreturnborrow rb 
                WHERE rb.bookId = b.bookId AND rb.returned = 'No'
            ) THEN 1
            ELSE 0
        END AS isBorrowed
    FROM tblbooks b
    LEFT JOIN tblauthor a ON b.authorId = a.authorId
    LEFT JOIN tblcategory c ON b.categoryId = c.categoryId";


// Apply filters dynamically
$conditions = [];
$params = [];
$types = '';

if ($categoryId) {
    $conditions[] = "b.categoryId = ?";
    $params[] = $categoryId;
    $types .= 'i';
}

if ($authorId) {
    $conditions[] = "b.authorId = ?";
    $params[] = $authorId;
    $types .= 'i';
}

if ($status) {
    $conditions[] = "b.status = ?";
    $params[] = $status;
    $types .= 's';
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Prepare and execute query
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error preparing query: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
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
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .add-btn {
            background-color: blue;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-btn:hover {
            background-color: darkblue;
        }
        .edit-btn {
            background-color: blue;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
                }
        .edit-btn:hover {
            background-color: darkblue;
        }
        .delete-btn {
            background-color: lightcoral;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        .filters {
            margin-bottom: 20px;
        }
        .filters h3 {
            display: inline-block;
            margin-right: 20px;
        }
        .filters a {
            color: blue;
            text-decoration: none;
        }
        .filters a:hover {
            text-decoration: underline;
        }
        
        /* Book Modal Styles */
        .book-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }
        .book-modal-content {
            position: relative;
            background: white;
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .book-cover {
            background: linear-gradient(45deg, #1a237e, #0d47a1);
            padding: 30px;
            border-radius: 5px;
            color: white;
            margin-bottom: 30px;
        }
        .book-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-top: 25px;
        }
        .book-detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 16px;
        }
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #dc3545;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .book-status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 1em;
            font-weight: bold;
            margin-top: 15px;
        }
        .status-available {
            background-color: #28a745;
            color: white;
        }
        .status-borrowed {
            background-color: #dc3545;
            color: white;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Books List</h6>
    </div>
    <div class="card-body">
        <button class="btn btn-primary" onclick="window.location.href='add-book.php'">Add New Book</button>
        <div style="margin-top: 20px;">
            <!-- Status Filter -->
            <div class="filters">
                <form method="GET" class="d-inline-block">
                    <select name="status" class="form-control d-inline-block" style="width: auto;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo $status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    <?php if ($categoryId): ?>
                        <input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
                    <?php endif; ?>
                    <?php if ($authorId): ?>
                        <input type="hidden" name="authorId" value="<?php echo $authorId; ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <!-- Display Active Filters -->
        <div class="filters">
            <?php if ($categoryId): ?>
                <h3>Showing Books in Category:
                    <span style="color: green;">
                        <?php
                        $categoryQuery = "SELECT categoryName FROM tblcategory WHERE categoryId = ?";
                        $stmt = $conn->prepare($categoryQuery);
                        $stmt->bind_param('i', $categoryId);
                        $stmt->execute();
                        $categoryResult = $stmt->get_result();
                        $categoryRow = $categoryResult->fetch_assoc();
                        echo htmlspecialchars($categoryRow['categoryName']);
                        ?>
                    </span>
                </h3>
                <a href="list-books.php<?php echo $authorId ? '?authorId=' . $authorId : ''; ?>">Remove Category Filter</a>
            <?php endif; ?>

            <?php if ($authorId): ?>
                <h3>Showing Books by Author:
                    <span style="color: blue;">
                        <?php
                        $authorQuery = "SELECT CONCAT(firstName, ' ', lastName) AS authorName FROM tblauthor WHERE authorId = ?";
                        $stmt = $conn->prepare($authorQuery);
                        $stmt->bind_param('i', $authorId);
                        $stmt->execute();
                        $authorResult = $stmt->get_result();
                        $authorRow = $authorResult->fetch_assoc();
                        echo htmlspecialchars($authorRow['authorName']);
                        ?>
                    </span>
                </h3>
                <a href="list-books.php<?php echo $categoryId ? '?categoryId=' . $categoryId : ''; ?>">Remove Author Filter</a>
            <?php endif; ?>

            <?php if (!$categoryId && !$authorId): ?>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table id="dataTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>APA Format</th>
                        <th>Author</th>
                        <th>Subject</th>
                        <th>Stocks</th>
                        <th>Publisher</th>
                        <th>Edition</th>
                        <th>Copyright</th>
                        <th>Accession No.</th>
                        <th>Barcode No.</th>
                        <th>Call No.</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['bookId'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                echo "<td>";
                                if ($row['authorName'] == 'No Author') {
                                    echo "<em>" . htmlspecialchars($row['title']) . "</em>. (" . htmlspecialchars($row['publishedYear']) . "). " . htmlspecialchars($row['publisher']);
                                } else {
                                    echo htmlspecialchars($row['authorName']) . " (" . htmlspecialchars($row['publishedYear']) . "). <em>" . htmlspecialchars($row['title']) . "</em>. " . htmlspecialchars($row['publisher']);
                                }
                                echo "</td>";
                                echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['publisher']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['edition'] ? $row['edition'] : 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars(substr($row['publishedDate'], 0, 4)) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['accessionNum']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['barcodeNum'] ? $row['barcodeNum'] : 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($row['callNum'] ? $row['callNum'] : 'N/A') . "</td>";
                                echo "<td><span class='badge " . ($row['status'] === 'Active' ? 'bg-success' : 'bg-danger') . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                                echo "<td class='btn-group'>";
                                echo "<button class='btn btn-info btn-sm' onclick='showBookModal(" . json_encode($row) . ")'>View</button>";
                                if ($row['isBorrowed']) {
                                    echo "<span style='color: red;'>Borrowed</span>";
                                } else {
                                    echo "<a href='edit-book.php?bookId=" . $row['bookId'] . "' class='btn btn-primary btn-sm'>Edit</a>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        }                    
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Book Modal -->
<div id="bookModal" class="book-modal">
    <div class="book-modal-content">
        <span class="close-modal" onclick="closeBookModal()">&times;</span>
        <div class="book-cover">
            <h2 id="modalTitle" style="font-size: 2.5em;"></h2>
            <p id="modalAuthor" class="mb-2" style="font-size: 1.5em;"></p>
            <div id="modalStatusBadge" class="book-status"></div>
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
            <div class="book-detail-item">
                <i class="fas fa-toggle-on"></i>
                <strong>Status:</strong>
                <span id="modalStatusText"></span>
            </div>
        </div>
    </div>
</div>

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        function showBookModal(bookData) {
            const modal = document.getElementById('bookModal');
            document.getElementById('modalTitle').textContent = bookData.title;
            document.getElementById('modalAuthor').textContent = `by ${bookData.authorName}`;
            document.getElementById('modalBookId').textContent = bookData.bookId;
            document.getElementById('modalCategory').textContent = bookData.categoryName;
            document.getElementById('modalPublisher').textContent = bookData.publisher;
            document.getElementById('modalPublishedDate').textContent = (bookData.publishedDate ? bookData.publishedDate.substring(0, 4) : '');
            document.getElementById('modalQuantity').textContent = bookData.quantity;
            document.getElementById('modalEdition').textContent = bookData.edition || 'N/A';
            
            // Set APA citation with italic title
            const apaCitation = bookData.authorName === 'No Author' 
                ? `<em>${bookData.title}</em>. (${bookData.publishedYear}). ${bookData.publisher}`
                : `${bookData.authorName} (${bookData.publishedYear}). <em>${bookData.title}</em>. ${bookData.publisher}`;
            document.getElementById('modalAPA').innerHTML = apaCitation;

            // Set status
            const statusElement = document.getElementById('modalStatusBadge');
            const statusTextElement = document.getElementById('modalStatusText');
            if (bookData.status === 'Active') {
                statusElement.textContent = 'Active';
                statusElement.className = 'book-status status-available';
                statusTextElement.textContent = 'Active';
            } else {
                statusElement.textContent = 'Inactive';
                statusElement.className = 'book-status status-borrowed';
                statusTextElement.textContent = 'Inactive';
            }

            modal.style.display = 'block';
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
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
