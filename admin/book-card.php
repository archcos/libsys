<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: sign-in.php');
    exit;
}

// Include your database connection file
include('process/db-connect.php'); 
ob_start();
$status = isset($_GET['status']) ? $_GET['status'] : ''; // Get the status from the URL parameter

// Query to fetch data from tblreturnborrow and join with tblborrowers, tblbooks, tblauthor, and tblcategory
$query = "
    SELECT 
        rb.borrowId, 
        rb.borrowedDate, 
        rb.returnDate, 
        rb.borrowerId, 
        rb.bookId,
        rb.returned,
        b.title AS bookTitle,
        CONCAT(a.firstName, ' ', a.lastName) AS bookAuthor,  -- Concatenate first and last names
        c.categoryName AS bookCategory,
        br.firstName,
        br.surName
    FROM 
        tblreturnborrow rb
    JOIN 
        tblborrowers br ON rb.borrowerId = br.idNumber
    JOIN 
        tblbooks b ON rb.bookId = b.bookId
    JOIN 
        tblauthor a ON b.authorId = a.authorId
    JOIN 
        tblcategory c ON b.categoryId = c.categoryId
    ORDER BY 
        rb.borrowedDate DESC  -- Order by borrowedDate in descending order (most recent first)
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returned Books</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        .delete-btn {
            background-color: lightcoral;
            color: white;
            border: none;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">

        <?php if ($status === 'success'): ?>
            <div class="message success" style="color: green; background: #dff0d8; padding: 10px; border: 1px solid #d0e9c6; margin-bottom: 20px;">
                <strong>Success!</strong> Email Sent Successfully!.
            </div>
        <?php elseif ($status === 'exists'): ?>
            <div class="message error" style="color: white; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
                <strong>Error!</strong> Email didn't send.
            </div>
        <?php elseif ($status === 'error'): ?>
            <div class="message fail" style="color: red; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
                <strong>Error!</strong> There was an issue with sending the email. Please try again.
            </div>
        <?php elseif ($status === 'noreturn'): ?>
            <div class="message fail" style="color: red; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
                <strong>Reminder!</strong> No books are due for return in the next 2 days.
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Borrowed/Returned Books</h6>
    </div>
    <div class="card-body">
        <h1>Borrowed/Returned Books</h1>
        <p>Below is the list of all returned books in the library.</p>
        <p>
            <form method="POST" action="process/send-email.php">
                <button type="submit" name="send_notifications" class="btn btn-primary">Send Return Reminders</button>
            </form>
        </p>

        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Date Slip</th>
                    <th>Book Card</th>
                    <th>ID Number</th>
                    <th>Borrowed Date</th>
                    <th>Return Date</th>
                    <th>Borrower Name</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $borrowedDate = new DateTime($row['borrowedDate']);
                            $formattedborrowedDate = $borrowedDate->format('F j, Y, g:i A');
                            $returnDate = new DateTime($row['returnDate']);
                            $formattedreturnDate = $returnDate->format('F j, Y');

                            echo "<tr>";
                            echo "<td><a href='pdf/generate-slip.php?borrowId=" . $row['borrowId'] . "' class='btn btn-info'>Print Slip</a></td>";
                            echo "<td><a href='pdf/generate-card.php?borrowId=" . $row['borrowId'] . "' class='btn btn-info'>Print Card</a></td>";
                            echo "<td>" . $row['borrowerId'] . "</td>";
                            echo "<td>" . $formattedborrowedDate . "</td>";
                            echo "<td>" . $formattedreturnDate . "</td>";
                            echo "<td>" . $row['firstName'] . " " . $row['surName'] . "</td>";
                            echo "<td>" . $row['bookTitle'] . "</td>";
                            echo "<td>" . $row['bookAuthor'] . "</td>";
                            echo "<td>" . $row['bookCategory'] . "</td>";
                            echo "<td>" . ($row['returned'] == "Yes" ? 'Yes' : 'No') . "</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- Toastr.js for success/failure notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>

        $(document).ready(function() {
        // Initialize DataTables
            $('#dataTable').DataTable();

            // Handle delete button click
            $('.delete-btn').click(function() {
                var borrowId = $(this).data('borrower-id');  // Get the borrowId from the button's data attribute
                console.log(borrowId)
                // Ask for confirmation before deleting
                if (confirm('Are you sure you want to delete this record?')) {
                    $.ajax({
                        url: 'process/delete-return.php',  // Path to your PHP delete script
                        type: 'POST',
                        data: { borrowId: borrowId },
                        success: function(response) {
                            if (response.success) {
                                alert('Record deleted successfully.');
                                location.reload();  // Reload the page to reflect the changes
                            } else {
                                alert('Error deleting the record.');
                            }
                        },
                        error: function() {
                            alert('Error processing the request.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
