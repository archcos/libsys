<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

include('process/db-connect.php'); // Database connection
ob_start();

// Get the receipt filter from the query string (default to "Yes")
$receiptFilter = isset($_GET['receipt']) ? $_GET['receipt'] : 'Yes';

// Fetch data based on the receipt filter
$query = "SELECT idNumber, borrowerType, libraryId, surName, firstName, middleName, emailAddress, remarks, receipt 
          FROM tblborrowers 
          WHERE receipt = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $receiptFilter);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers List</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
</head>
<body>
    <div class="container">
        <h1>
            <?php echo $receiptFilter === 'Yes' ? 'Receipt Log' : 'Non-Receipt Log'; ?>
        </h1>

        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Borrower Type</th>
                    <th>Library ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Email Address</th>
                    <th>Remarks</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['idNumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['borrowerType']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['libraryId']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['surName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['middleName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['emailAddress']) . "</td>";
                        echo "<td>" . ($row['remarks'] == 1 ? 'Yes' : 'No') . "</td>";
                        echo "<td>
                                <select class='receipt-dropdown' data-id='" . $row['idNumber'] . "'>
                                    <option value='Yes'" . ($row['receipt'] === 'Yes' ? ' selected' : '') . ">Yes</option>
                                    <option value='No'" . ($row['receipt'] === 'No' ? ' selected' : '') . ">No</option>
                                </select>
                              </td>";
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

            // Update receipt on dropdown change
            $('.receipt-dropdown').on('change', function() {
                const idNumber = $(this).data('id');
                const newReceipt = $(this).val();

                $.ajax({
                    url: 'process/update-receipt.php',
                    type: 'POST',
                    data: {
                        idNumber: idNumber,
                        receipt: newReceipt
                    },
                    success: function(response) {
                        location.reload(); // Refresh the page
                    },
                    error: function() {
                        alert('Failed to update receipt. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>