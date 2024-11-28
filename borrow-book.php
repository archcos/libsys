<?php
// Include your database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Fetch data from tblbooks
$query = "SELECT * FROM tblbooks";
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
        body {
            font-family: Arial, sans-serif;
        }
        .btn-action {
            padding: 5px 10px;
            background-color: #28a745; /* Green for active */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .btn-action:disabled {
            background-color: gray; /* Gray for disabled */
            cursor: not-allowed;
        }
        .btn-action.locked {
            background-color: gray;
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
                    <th>Action</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $isAvailable = $row['available']; // Check availability
                        echo "<tr>";
                        // Action button (clickable if available)
                        echo "<td>";
                        if ($isAvailable == 'Yes') {
                            echo "<button class='btn-action' onclick='handleAction(" . $row['bookId'] . ")'>Borrow</button>";
                        } else {
                            echo "<button class='btn-action locked' disabled>Borrowed</button>";
                        }
                        echo "</td>";
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['author'] . "</td>";
                        echo "<td>" . $row['available'] . "</td>";
                        echo "<td>
                                <button class='delete-btn' data-book-id='" . $row['bookId'] . "'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No books found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="borrowModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Borrow Book</h3>
            <form id="borrowForm">
                <input type="hidden" id="modalBookId">
                <label for="borrowerId">ID Number:</label><br>
                <input type="number" id="borrowerId" required><br><br>
                <label for="returnDate">Return Date:</label><br>
                <input type="date" id="returnDate" required><br><br>
                <button type="submit" class="btn-action">Approve</button>
                <button type="button" onclick="closeModal()" class="cancel-btn">Cancel</button>
            </form>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        function handleAction(bookId) {
        document.getElementById('modalBookId').value = bookId; // Store bookId in the modal
        document.getElementById('borrowModal').style.display = 'flex';
        }

        // Close the modal
        function closeModal() {
            document.getElementById('borrowModal').style.display = 'none';
        }

        // Handle borrow form submission
        document.getElementById('borrowForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const bookId = document.getElementById('modalBookId').value;
            const borrowerId = document.getElementById('borrowerId').value;
            const returnDate = document.getElementById('returnDate').value;

            // Send AJAX request to process borrowing
            $.ajax({
                url: 'transactions/borrow-book.php', // Backend script
                type: 'POST',
                data: {
                    bookId: bookId,
                    idNumber: borrowerId,
                    returnDate: returnDate
                },
                success: function(response) {
                    alert(response); // Show server response
                    closeModal();
                    location.reload(); // Reload the page
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        $(document).ready(function() {
            // Initialize DataTables
            $('#dataTable').DataTable();

            // Handle delete button click
            $('.delete-btn').on('click', function() {
                var bookId = $(this).data('book-id'); // Get Book ID

                // Show confirmation popup
                if (confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
                    // Send AJAX request to delete the book
                    $.ajax({
                        url: 'process/delete-book.php', // Backend script to handle delete
                        type: 'POST',
                        data: { bookId: bookId },
                        success: function(response) {
                            alert('Book deleted successfully!');
                            location.reload(); // Refresh the page
                        },
                        error: function() {
                            alert('Error deleting book. Please try again.');
                        }
                    });
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date();
            var day = String(today.getDate()).padStart(2, '0');  // Add leading zero if needed
            var month = String(today.getMonth() + 1).padStart(2, '0');  // Month is 0-based
            var year = today.getFullYear();

            // Format the date as YYYY-MM-DD
            var formattedDate = year + '-' + month + '-' + day;

            // Set the min attribute of the return date input
            document.getElementById('returnDate').setAttribute('min', formattedDate);
        });
    </script>
</body>
</html>
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
