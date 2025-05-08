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
$borrowerType = isset($_GET['borrowerType']) ? $_GET['borrowerType'] : null;

// Modify the query to filter by borrowerType if it is set
if ($borrowerType) {
    $query = "
    SELECT 
        b.*,
        CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM tblreturnborrow rb 
                WHERE rb.borrowerId = b.idNumber AND rb.returned = 'No'
            ) THEN 1
            ELSE 0
        END AS hasUnreturnedBooks
    FROM tblborrowers b
    " . ($borrowerType ? "WHERE borrowerType = ? " : "") . "
    ORDER BY dateRegistered DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $borrowerType);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default query if no borrowerType is specified
    $query = "SELECT * FROM tblborrowers";
    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower List</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        .add-btn {
            background-color: blue;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-btn:hover {
            background-color: darkblue;
        }
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
        .print-btn {
            background-color: green;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Borrower List</h6>
    </div>
    <div class="card-body">
        <?php if ($borrowerType): ?>
            <h2>Borrower List: <?php echo htmlspecialchars($borrowerType); ?></h2>
        <?php endif; ?>
        <button class="btn btn-success" onclick="openGenerateList()">Generate List</button>
        <button class="btn btn-primary" href="#" data-toggle="modal" data-target="#addBorrowerModal">Add New Borrower</button>
        <p>Below is the list of registered borrowers.</p>
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
                    <th>Action</th>
                    </tr>
            </thead>
            <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $remarksStatus = ($row['remarks'] == 'Activated') ? 'Activated' : 'Deactivated';
                            echo "<tr>";
                            echo "<td>
                                    <a href='pdf/generate-pdf.php?idNumber=" . $row['idNumber'] . "'>
                                        <button class='btn btn-success btn-sm'>Borrowers Card</button>
                                    </a>
                                </td>";
                            echo "<td>" . $row['libraryId'] . "</td>";
                            echo "<td>" . $row['idNumber'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['surName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
                            echo "<td>" . $row['gender'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['emailAddress']) . "</td>";
                            echo "<td>
                                    <select class='remarks-dropdown' data-borrower-id='" . $row['idNumber'] . "'>
                                        <option value='Activated' " . ($row['remarks'] == 'Activated' ? 'selected' : '') . ">Activated</option>
                                        <option value='Deactivated' " . ($row['remarks'] == 'Deactivated' ? 'selected' : '') . ">Deactivated</option>
                                    </select>
                                </td>";
                            echo "<td>
                                <a href='edit-borrower.php?idNumber=" . $row['idNumber'] . "'>
                                    <button class='btn btn-primary btn-sm'>Edit</button>
                                </a>
                              </td>";

                            echo "</tr>";
                        }
                    }

                ?>
            </tbody>
        </table>
    </div>
</div>

        <!-- Modal - Add Borrower -->
        <div class="modal fade" id="addBorrowerModal" tabindex="-1" role="dialog" aria-labelledby="addBorrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBorrowerModalLabel">Select Borrower Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Please select the type of borrower:</p>
                    <div class="btn-group btn-group-toggle d-flex justify-content-center" data-toggle="buttons">
                        <label class="btn btn-outline-primary mx-2">
                            <input type="radio" name="borrowerType" value="Student" id="studentOption"> Student
                        </label>
                        <label class="btn btn-outline-primary mx-2">
                            <input type="radio" name="borrowerType" value="Faculty" id="facultyOption"> Faculty
                        </label>
                        <label class="btn btn-outline-primary mx-2">
                            <input type="radio" name="borrowerType" value="Staff" id="staffOption"> Staff
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="proceedButton">Proceed</button>
                </div>
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables and Handle Delete -->
    <script>

        function openGenerateList() {
            window.location.href = 'pdf/generate-list.php';
        }

        document.getElementById('proceedButton').addEventListener('click', function () {
            const borrowerType = document.querySelector('input[name="borrowerType"]:checked');
            if (borrowerType) {
                const selectedType = borrowerType.value;
                window.location.href = `add-borrower.php?borrowerType=${selectedType}`;
            } else {
                alert('Please select a borrower type before proceeding.');
            }
        });

       
        $(document).ready(function() {
            // Initialize DataTables
            $('#dataTable').DataTable();
            $(document).ready(function() {
            $('#openModalBtn').on('click', function() {
                $('#approveModal').modal('show');
            });

            $('#approveBtn').on('click', function() {
                $('#approveModal').modal('hide');
            });
           });
            
 
            // Handle remarks change (Activated/Deactivated)
            $('.remarks-dropdown').on('change', function() {
                var idNumber = parseInt($(this).data('borrower-id'), 10); // Ensure it's an integer
                var remarksValue = $(this).val(); // Convert to integer (1 for Activated, 0 for Deactivated)

                console.log(idNumber, remarksValue)
                // Send AJAX request to update the remarks in the database
               $.ajax({
                    url: 'process/update-remarks.php',
                    type: 'POST',
                    data: {
                        idNumber: idNumber,
                        remarks: remarksValue
                    },
                    success: function(response) {
                        console.log('Success response:', response); // Debug response
                        alert('Remarks updated successfully!');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error response:', xhr.responseText); // Debug error
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
