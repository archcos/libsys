<?php
// Include your database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Query to fetch data from tblreturnborrow and join with tblborrowers and tblbooks
$query = "
    SELECT 
        rb.borrowId, 
        rb.borrowedDate, 
        rb.returnDate, 
        rb.borrowerId, 
        rb.bookId,
        rb.returned,
        b.title AS bookTitle,
        b.author AS bookAuthor,
        br.firstName,
        br.surName
    FROM 
        tblreturnborrow rb
    JOIN 
        tblborrowers br ON rb.borrowerId = br.idNumber
    JOIN 
        tblbooks b ON rb.bookId = b.bookId
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

</head>
<body>
    <div class="container">
        <h1>Borrowed/Returned Books</h1>
        <p>Below is the list of all returned books in the library.</p>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Borrower ID</th>
                    <th>Borrowed Date</th>
                    <th>Return Date</th>
                    <th>Borrower Name</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['borrowerId'] . "</td>"; // Borrow ID
                        echo "<td>" . $row['borrowedDate'] . "</td>"; // Borrowed Date
                        echo "<td>" . $row['returnDate'] . "</td>"; // Return Date
                        echo "<td>" . $row['firstName'] . " " . $row['surName'] . "</td>"; // Borrower Name
                        echo "<td>" . $row['bookTitle'] . "</td>"; // Book Title
                        echo "<td>" . $row['bookAuthor'] . "</td>"; // Author
                        echo "<td>" . $row['returned'] . "</td>"; // Returned
                        echo "</tr>";
                    }
                } else {
                    // Fix colspan to match the number of columns in <thead>
                    // echo "<tr><td colspan='8' class='text-center'>No returned books found</td></tr>";
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
