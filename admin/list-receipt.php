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

// Get filter parameters
$borrowerType = isset($_GET['borrowerType']) ? $_GET['borrowerType'] : '';
$remarks = isset($_GET['remarks']) ? $_GET['remarks'] : '';

// Build the query with filters
$query = "SELECT idNumber, borrowerType, libraryId, surName, firstName, middleName, emailAddress, remarks, receipt 
          FROM tblborrowers 
          WHERE receipt = ?";
$params = [$receiptFilter];
$types = "s";

if (!empty($borrowerType)) {
    $query .= " AND borrowerType = ?";
    $params[] = $borrowerType;
    $types .= "s";
}

if (!empty($remarks)) {
    $query .= " AND remarks = ?";
    $params[] = $remarks;
    $types .= "s";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
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
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $receiptFilter === 'Yes' ? 'Receipt Log' : 'Non-Receipt Log'; ?></h6>
    </div>
    <div class="card-body">
        <h1><?php echo $receiptFilter === 'Yes' ? 'Receipt Log' : 'Non-Receipt Log'; ?></h1>
        
        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="receipt" value="<?php echo $receiptFilter; ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="borrowerType">Borrower Type:</label>
                        <select name="borrowerType" id="borrowerType" class="form-control">
                            <option value="">All</option>
                            <option value="Student" <?php echo $borrowerType === 'Student' ? 'selected' : ''; ?>>Student</option>
                            <option value="Faculty" <?php echo $borrowerType === 'Faculty' ? 'selected' : ''; ?>>Faculty</option>
                            <option value="Staff" <?php echo $borrowerType === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="remarks">Status:</label>
                        <select name="remarks" id="remarks" class="form-control">
                            <option value="">All</option>
                            <option value="Activated" <?php echo $remarks === 'Activated' ? 'selected' : ''; ?>>Activated</option>
                            <option value="Deactivated" <?php echo $remarks === 'Deactivated' ? 'selected' : ''; ?>>Deactivated</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="?receipt=<?php echo $receiptFilter; ?>" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <button id="fetchReceiptData" class="btn btn-success">Print Receipt Data</button>
        <div style="margin-top: 20px;"> 
        </div>

        <div class="table-responsive">
        <table id="dataTable" class="display table" style="width:100%">
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
                    <?php if ($receiptFilter !== 'Yes'): ?>
                        <th>Receipt</th> <?php endif; ?>
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
                        echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";

                        if ($receiptFilter !== 'Yes') {
                            echo "<td>
                                    <select class='receipt-dropdown' data-id='" . $row['idNumber'] . "'>
                                        <option value='Yes'" . ($row['receipt'] === 'Yes' ? ' selected' : '') . ">Yes</option>
                                        <option value='No'" . ($row['receipt'] === 'No' ? ' selected' : '') . ">No</option>
                                    </select>
                                </td>";
                        }

                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
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

        document.getElementById('fetchReceiptData').addEventListener('click', function() {
            const receiptFilter = "<?php echo $receiptFilter; ?>"; // Get PHP variable in JS
            const borrowerType = "<?php echo $borrowerType; ?>";
            const remarks = "<?php echo $remarks; ?>";
            
            // Build URL with filters
            let targetUrl = receiptFilter === "Yes" ? 'pdf/generate-receipt.php' : 'pdf/generate-nonreceipt.php';
            targetUrl += '?';
            
            if (borrowerType) targetUrl += 'borrowerType=' + encodeURIComponent(borrowerType) + '&';
            if (remarks) targetUrl += 'remarks=' + encodeURIComponent(remarks);
            
            window.location.href = targetUrl;
        });

    </script>
</body>
</html>
<?php
$content = ob_get_clean();
include('templates/main.php');
?>
