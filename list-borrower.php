<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: sign-in.php');  // Change 'login.php' to your login page
    exit;  // Make sure the script stops executing after the redirect
}

// Include your database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Fetch data from tblborrowers
$query = "SELECT * FROM tblborrowers";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower List</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Custom styles -->
    <style>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Borrower List</h1>
        <p>Below is the list of all registered borrowers in the system.</p>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Print</th>
                    <th>Library ID</th>
                    <th>ID Number</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Gender</th>
                    <th>Email Address</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $remarksStatus = ($row['remarks'] == 1) ? 'Activated' : 'Deactivated';
                        echo "<tr>";
                        echo "<td>
                                <a href='pdf/generate-pdf.php?idNumber=" . $row['idNumber'] . "'>
                                    <button class='print-btn'>Print</button>
                                </a>
                              </td>";
                        echo "<td>" . $row['libraryId'] . "</td>";
                        echo "<td>" . $row['idNumber'] . "</td>";
                        echo "<td>" . $row['surName'] . "</td>";
                        echo "<td>" . $row['firstName'] . "</td>";
                        echo "<td>" . $row['gender'] . "</td>";
                        echo "<td>" . $row['emailAddress'] . "</td>";
                        echo "<td>
                                <select class='remarks-dropdown' data-borrower-id='" . $row['idNumber'] . "'>
                                    <option value='1' " . ($row['remarks'] == 1 ? 'selected' : '') . ">Activated</option>
                                    <option value='0' " . ($row['remarks'] == 0 ? 'selected' : '') . ">Deactivated</option>
                                </select>
                              </td>";
                        echo "<td>
                                <button class='delete-btn' data-borrower-id='" . $row['idNumber'] . "'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No borrowers found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables and Handle Delete -->
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#dataTable').DataTable();

            // Handle delete button click
            $('.delete-btn').on('click', function() {
                var idNumber = $(this).data('borrower-id'); // Get Library ID
                console.log(idNumber)

                // Show confirmation popup
                if (confirm('Are you sure you want to delete this borrower? This action cannot be undone.')) {
                    // Send AJAX request to delete the borrower
                    $.ajax({
                        url: 'process/delete-borrower.php', // Backend script to handle delete
                        type: 'POST',
                        data: { idNumber: idNumber },
                        success: function(response) {
                            alert('Borrower deleted successfully!');
                            location.reload(); // Refresh the page
                            console.log(response)
                        },
                        error: function() {
                            alert('Error deleting borrower. Please try again.');
                        }
                    });
                }
            });

            // Handle remarks change (Activated/Deactivated)
            $('.remarks-dropdown').on('change', function() {
                var idNumber = $(this).data('borrower-id');
                var remarksValue = parseInt($(this).val(), 10); // Convert to integer (1 for Activated, 0 for Deactivated)

                console.log(idNumber, remarksValue)
                // Send AJAX request to update the remarks in the database
                $.ajax({
                    url: 'process/update-remarks.php', // Backend script to handle remarks update
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
        });
    </script>
</body>
</html>
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
