<?php
// Include your database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file

// Fetch data from tblborrowers
$query = "SELECT * FROM tblborrowers";
$result = $conn->query($query);

// Start output buffering to inject this content into the main template
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower List</title>
    
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <!-- Custom styles for this page -->
    <style>
        .table thead th {
            background-color: #4e73df;
            color: white;
        }

        .remarks-dropdown {
            width: 120px;
            cursor: pointer;
        }

        .delete-btn {
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body id="page-top">

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800 text-center">Borrower List</h1>
        <p class="mb-4 text-center">Below is the list of all registered borrowers in the system.</p>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Borrower Details</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Print</th> <!-- Print button column -->
                                <th>Library ID</th>
                                <th>ID Number</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Gender</th>
                                <th>Home Address</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Determine the remarks status
                                    $remarksStatus = ($row['remarks'] == 1) ? 'Activated' : 'Deactivated';

                                    echo "<tr>";
                                    echo "<td>
                                    <a href='pdf/generate-pdf.php?idNumber=" . $row['idNumber']. "'>
                                      <button class='print-btn'>Print</button>
                                    </a>
                                    </td>";
                                    echo "<td>" . $row['libraryId'] . "</td>";
                                    echo "<td>" . $row['idNumber'] . "</td>";
                                    echo "<td>" . $row['surName'] . "</td>";
                                    echo "<td>" . $row['firstName'] . "</td>";
                                    echo "<td>" . $row['gender'] . "</td>";
                                    echo "<td>" . $row['homeAddress'] . "</td>";
                                    echo "<td>
                                            <select class='remarks-dropdown' data-borrower-id='" . $row['libraryId'] . "'>
                                                <option value='1' " . ($row['remarks'] == 1 ? 'selected' : '') . ">Activated</option>
                                                <option value='2' " . ($row['remarks'] == 2 ? 'selected' : '') . ">Deactivated</option>
                                            </select>
                                          </td>";
                                    echo "<td><span class='delete-btn' data-borrower-id='" . $row['libraryId'] . "'>Delete</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No borrowers found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- End of Page Content -->

    
    <!-- Optional: DataTables JS for sorting and pagination -->
    <script>
    $(document).ready(function() {
        // Initialize the DataTable
        $('#dataTable').DataTable();

        // Handle remarks update
        $('.remarks-dropdown').on('change', function() {
            var idNumber = $(this).data('borrower-id');
            var remarksValue = $(this).val();
            
            $.ajax({
                url: 'editing-borrower.php',
                type: 'POST',
                data: {
                    idNumber: idNumber,
                    remarks: remarksValue
                },
                success: function(response) {
                    alert('Remarks updated successfully!');
                },
                error: function() {
                    alert('Error updating remarks.');
                }
            });
        });

        // Handle delete action with confirmation
        $('.delete-btn').on('click', function() {
            var idNumber = $(this).data('borrower-id'); // Get Library ID
            
            // Show confirmation popup
            if (confirm('Are you sure you want to delete this borrower? This action cannot be undone.')) {
                // Send an AJAX request to delete the borrower
                $.ajax({
                    url: 'process/delete-borrower.php', // Your backend delete script
                    type: 'POST',
                    data: { idNumber: idNumber }, // Send Library ID to delete
                    success: function(response) {
                        alert('Borrower deleted successfully!');
                        location.reload(); // Reload page to reflect changes
                    },
                    error: function() {
                        alert('Error deleting borrower. Please try again.');
                    }
                });
            } else {
                console.log('Delete action canceled.'); // User canceled the delete
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
