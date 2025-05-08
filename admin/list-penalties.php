<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

ob_start();

include('process/db-connect.php'); // Adjust the path to your actual connection file

// Fetch penalties along with borrower names, book titles, and paid status from the database
$query = "
    SELECT 
        p.penaltyId, 
        p.borrowerId, 
        p.penalty, 
        p.cost, 
        p.paid,
        p.bookId,
        CONCAT(b.firstName, ' ', b.surName) AS borrowerName,
        bk.title
    FROM 
        tblpenalties p
    JOIN 
        tblborrowers b ON p.borrowerId = b.idNumber
    JOIN 
        tblbooks bk ON p.bookId = bk.bookId
    ORDER BY 
        p.penaltyId DESC
";
$result = $conn->query($query);


// Handle the change in "paid" status via AJAX
if (isset($_POST['penaltyId']) && isset($_POST['paid'])) {
    $penaltyId = $_POST['penaltyId'];
    $paid = $_POST['paid'];

    // Update the "paid" status in the database
    $updateQuery = "UPDATE tblpenalties SET paid = ? WHERE penaltyId = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $paid, $penaltyId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penalties List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Penalties List</h6>
    </div>
    <div class="card-body">
        <div class="container mt-5">
            <h2 class="mb-4">Penalties List</h2>
            <table id="dataTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Penalty ID</th>
                        <th>Borrower ID</th>
                        <th>Borrower Name</th>
                        <th>Book Title</th>
                        <th>Penalty</th>
                        <th>Cost</th>
                        <th>Paid Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if any results were returned
                    if ($result->num_rows > 0) {
                        // Loop through each row and display the penalty details
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['penaltyId'] . "</td>";
                            echo "<td>" . $row['borrowerId'] . "</td>";
                            echo "<td>" . $row['borrowerName'] . "</td>";
                            echo "<td>" . $row['title'] . "</td>"; // Displaying book title
                            echo "<td>" . $row['penalty'] . "</td>";
                            echo "<td>" . number_format($row['cost'], 2) . "</td>";
                            echo "<td><select class='form-control paidStatus' data-penalty-id='" . $row['penaltyId'] . "'>
                                            <option value='Yes' " . ($row['paid'] == 'Yes' ? 'selected' : '') . ">Paid</option>
                                            <option value='No' " . ($row['paid'] == 'No' ? 'selected' : '') . ">Unpaid</option>
                                        </select></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No penalties found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- jQuery, DataTables and Bootstrap JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#dataTable').DataTable();

            // Handle change in "Paid" status
            $('.paidStatus').on('change', function() {
                var penaltyId = $(this).data('penalty-id');
                var paid = $(this).val();

                // Send the update via AJAX
                $.post('list-penalties.php', { penaltyId: penaltyId, paid: paid }, function(response) {
                    if (response == 'success') {
                        alert('Paid status updated successfully.');
                    } else {
                        alert('Error updating paid status. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
// Close the connection
$conn->close();
$content = ob_get_clean();
include('templates/main.php');
?>
