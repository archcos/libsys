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
    $query = "SELECT * FROM tblborrowers WHERE borrowerType = ?  ORDER BY dateRegistered DESC";
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
    </style>
</head>
<body>
    <div class="container">
            <?php if ($borrowerType): ?>
                <h2>Borrower List: <?php echo htmlspecialchars($borrowerType); ?></h2>
            <?php endif; ?>
        <button class="btn add-btn" href="#" data-toggle="modal" data-target="#addBorrowerModal">Add New Borrower</button>
        <button class="btn add-btn" href="#" data-toggle="modal" data-target="#borrowerModal">Edit Borrower</button>
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
                }
                ?>
            </tbody>
        </table>
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
    <!-- Modal - Edit Borrower -->
    <div class="modal fade" id="borrowerModal" tabindex="-1" role="dialog" aria-labelledby="borrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="borrowerModalLabel">Enter Borrower ID</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="idNumber">Borrower ID</label>
                        <input type="text" class="form-control" id="idNumber" placeholder="Enter Borrower ID">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="checkidNumber">Check Borrower ID</button>
                </div>
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables and Handle Delete -->
    <script>
        document.getElementById('proceedButton').addEventListener('click', function () {
            const borrowerType = document.querySelector('input[name="borrowerType"]:checked');
            if (borrowerType) {
                const selectedType = borrowerType.value;
                window.location.href = `add-borrower.php?borrowerType=${selectedType}`;
            } else {
                alert('Please select a borrower type before proceeding.');
            }
        });

        $(document).ready(function () {
            $('#checkidNumber').on('click', function () {
                const idNumber = $('#idNumber').val().trim();
                if (idNumber) {
                    $.ajax({
                        url: 'process/edit-check.php',
                        type: 'POST',
                        data: { idNumber: idNumber },
                        success: function (response) {
                            const data = JSON.parse(response);
                            if (data.exists) {
                                window.location.href = `edit-borrower.php?idNumber=${idNumber}`;
                            } else {
                                alert('Borrower ID does not exist. Please try again.');
                            }
                        },
                        error: function () {
                            alert('An error occurred while checking the Borrower ID.');
                        }
                    });
                } else {
                    alert('Please enter a Borrower ID.');
                }
            });

            $('#borrowerModal').on('hidden.bs.modal', function () {
                $('#idNumber').val('');
            });
        });
        
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
