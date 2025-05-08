<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

include('db/db-connect.php'); // Database connection
ob_start();

// Get logged-in user's ID
$userId = $_SESSION['user_id']; 

// Check if 'returned' query parameter is set
$returnedFilter = isset($_GET['returned']) ? $_GET['returned'] : null;

// Prepare the base query
$query = "SELECT b.bookId, b.title,
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName, b.publisher, b.publishedDate,
                 rb.borrowedDate, rb.returnDate, rb.returned
          FROM tblbooks b
          JOIN tblauthor a ON b.authorId = a.authorId
          JOIN tblcategory c ON b.categoryId = c.categoryId
          JOIN tblreturnborrow rb ON b.bookId = rb.bookId
          WHERE rb.borrowerId = ?";

// Apply the returned filter if set
if ($returnedFilter === 'Yes') {
    $query .= " AND rb.returned = 'Yes'"; // Filter for books that are returned
} elseif ($returnedFilter === 'No') {
    $query .= " AND (rb.returned IS NULL OR rb.returned != 'Yes')"; // Filter for books that are not returned
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
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
    </style>
</head>
<body>
    <div class="container">
        <h3>Books List</h3>

        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Publisher</th>
                    <th>Published Date</th>
                    <th>Borrowed Date</th>
                    <th>Return Date</th>
                    <th>Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $borrowedDate = new DateTime($row['borrowedDate']);
                        $formattedborrowedDate = $borrowedDate->format('F j, Y, g:i A'); // Format: 12-hour time with AM/PM
                        $returnDate = new DateTime($row['returnDate']);
                        $formattedreturnDate = $returnDate->format('F j, Y'); // Format: December 19, 2024
                        

                        $returnedStatus = $row['returned'] ? 'Yes' : 'No'; // Check if returned is 'Yes'
                        echo "<tr>";
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['publisher']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['publishedDate']) . "</td>";
                        echo "<td>" . htmlspecialchars($formattedborrowedDate) . "</td>";
                        echo "<td>" . htmlspecialchars($formattedreturnDate) . "</td>";
                        echo "<td>" . htmlspecialchars($row['returned']) . "</td>";
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
            $('#dataTable').DataTable({
                order: [[7, 'desc']], // 7 is the index of the 'Borrowed Date' column (adjust based on your table structure)
            });
        });

    </script>
</body>
</html>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
