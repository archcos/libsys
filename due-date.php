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
            <div class="message error" style="color: white; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
                <strong>Error!</strong> There was an issue with sending the email. Please try again.
            </div>
        <?php endif; ?>

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
                    <th>Date Slip</th> <!-- Added a new column for actions -->
                    <th>Book Card</th> <!-- Added a new column for actions -->
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
                        echo "<tr>";
                        echo "<td><a href='pdf/generate-slip.php?borrowId=" . $row['borrowId'] . "' class='btn btn-info'>Print Slip</a></td>"; // Print Button with borrowId
                        echo "<td><a href='pdf/generate-card.php?borrowId=" . $row['borrowId'] . "' class='btn btn-info'>Print Card</a></td>"; // Print Button with borrowId
                        echo "<td>" . $row['borrowerId'] . "</td>"; // Borrower ID
                        echo "<td>" . $row['borrowedDate'] . "</td>"; // Borrowed Date
                        echo "<td>" . $row['returnDate'] . "</td>"; // Return Date
                        echo "<td>" . $row['firstName'] . " " . $row['surName'] . "</td>"; // Borrower Name
                        echo "<td>" . $row['bookTitle'] . "</td>"; // Book Title
                        echo "<td>" . $row['bookAuthor'] . "</td>"; // Author
                        echo "<td>" . $row['bookCategory'] . "</td>"; // Category
                        echo "<td>" . ($row['returned'] == "Yes" ? 'Yes' : 'No') . "</td>"; // Returned
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr.js for success/failure notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
       $(document).ready(function() {
            $('#dataTable').DataTable(); // Matches <table id="dataTable">
        });
    </script>
</body>
</html>

<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
