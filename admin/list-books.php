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

// Prepare base query
$query = "
    SELECT 
        b.bookId, b.title, b.quantity, 
        CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
        c.categoryName, b.publisher, b.publishedDate,
        CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM tblreturnborrow rb 
                WHERE rb.bookId = b.bookId AND rb.returned = 'No'
            ) THEN 1
            ELSE 0
        END AS isBorrowed
    FROM tblbooks b
    JOIN tblauthor a ON b.authorId = a.authorId
    JOIN tblcategory c ON b.categoryId = c.categoryId";


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

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Prepare and execute query
$stmt = $conn->prepare($query);
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
        
    </style>
</head>
<body>
    <div class="container">
        <h1>Books List</h1>
        <button class="btn btn-primary" onclick="window.location.href='add-book.php'">Add New Book</button>
        <div style="margin-top: 20px;">  
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

        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Stocks</th>
                    <th>Publisher</th>
                    <th>Published Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['bookId'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['publisher']) . "</td>"; 
                            echo "<td>" . htmlspecialchars($row['publishedDate']) . "</td>"; 
                            echo "<td>";
                            if ($row['isBorrowed']) {
                                echo "<span style='color: red;'>Borrowed - Cannot Delete</span>";
                            } else {
                                echo "<a href='edit-book.php?bookId=" . $row['bookId'] . "' class='btn edit-btn'>Edit</a>";
                                echo "<button class='delete-btn' data-book-id='" . $row['bookId'] . "'>Delete</button>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    }                    

                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
            $(document).ready(function() {
            $('#openModalBtn').on('click', function() {
                $('#approveModal').modal('show');
            });

            $('#approveBtn').on('click', function() {
                $('#approveModal').modal('hide');
            });
           });
            // Delete book functionality
            $('.delete-btn').on('click', function() {
            const bookId = $(this).data('book-id');
            const row = $(this).closest('tr');
            const isBorrowed = row.find('td:contains("Borrowed - Cannot Delete")').length > 0;

            if (isBorrowed) {
                alert('This book is currently borrowed and cannot be deleted.');
                return;
            }

            if (confirm('Are you sure you want to delete this book?')) {
                $.ajax({
                    url: 'process/delete-book.php',
                    type: 'POST',
                    data: { bookId: bookId },
                    success: function(response) {
                      
                        location.reload();
                    },
                    error: function() {
                        alert('Error deleting book. Please try again.');
                    }
                });
            }
        });

        });
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
