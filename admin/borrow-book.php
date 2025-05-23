<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: sign-in.php'); // Change 'login.php' to your login page
    exit; // Stop script execution after the redirect
}

// Include your database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file
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
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
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
        .modal {
            display: none;
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .modal-content form {
            margin: 0;
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
                    <!-- <th>Return</th> -->
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
                        // echo "<td>";
                        // echo "<button class='btn-action return-btn' onclick='handleReturn(" . $row['bookId'] . ")'>Return</button>";
                        // echo "</td>";

                        // Book details
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                    }
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
                <label for="borrowerIds">ID Number:</label><br>
                <input type="number" id="borrowerIds" required><br><br>
                <label for="librarianName">Librarian Full Name:</label><br>
                <input type="text" id="librarianName" required><br><br>
                <label for="formattedReturnDate">Return Date:</label><br>
                <input type="text" id="formattedReturnDate" readonly><br>
                <input type="date" id="returnDate" name="returnDate" readonly required style="display: none;"><br>
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
                <label for="returnerIds">ID Number:</label><br>
                <input type="number" id="returnerIds" required><br><br>
                <button type="submit" class="btn-action">Confirm Return</button>
                <button type="button" onclick="closeModal('returnModal')" class="cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        
        $(document).ready(function() {
            // Initialize DataTables
            $('#dataTable').DataTable();

        });
                // Handle Borrow
                // Open Borrow Modal
// Handle Borrow
// Handle Borrow
function handleBorrow(bookId) {
    const borrowModal = document.getElementById('borrowModal');
    const returnDateInput = document.getElementById('returnDate');
    const formattedReturnDateInput = document.getElementById('formattedReturnDate');
    const borrowerIdInput = document.getElementById('borrowerIds');

    // Clear the return date fields when opening the modal
    returnDateInput.value = '';
    formattedReturnDateInput.value = '';

    // Set bookId and display the modal
    document.getElementById('borrowModalBookId').value = bookId;
    borrowModal.style.display = 'flex';
}

// Handle the input of borrower ID
document.getElementById('borrowerIds').addEventListener('input', function () {
    const borrowerId = this.value;

    // Only proceed if borrower ID is provided
    if (borrowerId) {
        const returnDateInput = document.getElementById('returnDate');
        const formattedReturnDateInput = document.getElementById('formattedReturnDate');

        // Send AJAX request to check borrower type
        $.ajax({
            url: 'transactions/check-borrower-type.php',  // PHP script to fetch borrower type
            type: 'POST',
            data: { borrowerId },
            success: function (response) {
                console.log('Borrower Type:', response);  // Log the response to check if it's correct
                const borrowerType = response.trim(); // Ensure it's trimmed for any extra spaces

                // Set the return date based on borrower type
                const today = new Date();
                let returnDate = new Date(today);

                if (borrowerType === 'Student') {
                    returnDate.setDate(today.getDate() + 3); // 3 days for students
                } else {
                    returnDate.setDate(today.getDate() + 80); // 80 days for others
                }

                // Format the return date as MM/DD/YYYY for display
                const displayDay = String(returnDate.getDate()).padStart(2, '0');
                const displayMonth = String(returnDate.getMonth() + 1).padStart(2, '0'); // Month is 0-based
                const displayYear = returnDate.getFullYear();
                const formattedDisplayDate = `${displayMonth}/${displayDay}/${displayYear}`;

                // Format the return date as YYYY-MM-DD for database
                const dbDay = String(returnDate.getDate()).padStart(2, '0');
                const dbMonth = String(returnDate.getMonth() + 1).padStart(2, '0');
                const dbYear = returnDate.getFullYear();
                const dbFormattedDate = `${dbYear}-${dbMonth}-${dbDay}`;

                // Set the calculated return date for display and database
                formattedReturnDateInput.value = formattedDisplayDate;
                returnDateInput.value = dbFormattedDate;
            },
            error: function () {
                alert('Error fetching borrower type.');
            }
        });
    }
});





        // Open Return Modal
        function handleReturn(bookId) {
            document.getElementById('returnModalBookId').value = bookId; // Store bookId in the modal
            document.getElementById('returnModal').style.display = 'flex';
        }

        // Close Modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Handle Borrow Form Submission
        document.getElementById('borrowForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const bookId = document.getElementById('borrowModalBookId').value;
            const borrowerId = document.getElementById('borrowerIds').value;
            const returnDate = document.getElementById('returnDate').value;
            const librarianName = document.getElementById('librarianName').value;


            // Send AJAX request for borrowing
            $.ajax({
                url: 'transactions/borrow-book.php',
                type: 'POST',
                data: { bookId, idNumber: borrowerId, librarianName, returnDate },
                success: function (response) {
                    alert(response);
                    closeModal('borrowModal');
                    location.reload();
                },
                error: function () {
                    alert('Error borrowing the book. Please try again.');
                },
            });
        });

        // Handle Return Form Submission
        document.getElementById('returnForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const bookId = document.getElementById('returnModalBookId').value;
            const returnerId = document.getElementById('returnerIds').value;

            // Send AJAX request for returning
            $.ajax({
                url: 'transactions/return-book.php',
                type: 'POST',
                data: { bookId, idNumber: returnerId },
                success: function (response) {
                    alert(response);
                    closeModal('returnModal');
                    location.reload();
                },
                error: function () {
                    alert('Error returning the book. Please try again.');
                },
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
$content = ob_get_clean();
include('templates/main.php');
?>
