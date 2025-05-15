<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

include('process/db-connect.php');
ob_start();

// Fetch all reference slips along with borrower information
$query = "
    SELECT rs.referenceId, 
           CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
           b.title AS bookTitle, 
           rs.type,
           rs.callNumber,
           rs.subLocation,
           rs.category, 
           rs.date, 
           borrower.firstName, borrower .surName
    FROM tblreference rs
    JOIN tblborrowers borrower ON rs.borrowerId = borrower.idNumber
    JOIN tblauthor a ON rs.author = a.authorId
    JOIN tblbooks b ON rs.title = b.bookId
";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reference Slips</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Material Design for Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        .print-btn {
            background-color: green;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        .print-btn:hover {
            background-color: darkgreen;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }
        .add-btn {
            background-color: blue;
        }
        .edit-btn {
            background-color: blue;
        }
        .delete-btn {
            background-color: lightcoral;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        .delete-btn {
            background-color: lightcoral;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        .cancel-btn {
            background-color: lightcoral;
        }
        .cancel-btn:hover {
            background-color: darkred;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-height: 600px;
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control, .form-select {
            width: 100%;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-group {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
    </style>
</head>

<?php
// Fetch authors with full name (firstName + lastName)
$authorsQuery = "SELECT authorId, CONCAT(firstName, ' ', lastName) AS authorName FROM tblauthor";
$authorsResult = $conn->query($authorsQuery);
?>

<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Manage Reference Slips</h6>
    </div>
    <div class="card-body">
        <h1>Manage Reference Slips</h1>
        <button class="btn btn-primary" onclick="showAddReferenceModal()">Add New Reference Slip</button>
        <div style="margin-top: 20px;"> 
        </div>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Print</th>
                    <th>Reference ID</th>
                    <th>Borrower Name</th>
                    <th>Author</th>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Call Number</th>
                    <th>SubLocation</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $borrowerName = htmlspecialchars($row['firstName'] . ' ' . $row['surName']);
                            echo "<tr>";
                            echo "<td>
                                    <button class='btn print-btn' onclick='printReferenceSlip(" . $row['referenceId'] . ")'>Print</button>
                                </td>";
                            echo "<td>" . $row['referenceId'] . "</td>";
                            echo "<td>" . $borrowerName . "</td>";
                            echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['bookTitle']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['callNumber']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['subLocation']) . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>
                                    <div style='display: flex; gap: 5px;'>
                                        <button class='btn btn-primary' onclick='editReferenceSlip(" . $row['referenceId'] . ")'>Edit</button>
                                        <button class='btn delete-btn' data-reference-id='" . $row['referenceId'] . "'>Delete</button>
                                    </div>
                                </td>";
                            echo "</tr>"; 
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Add Reference Slip Modal -->
    <div id="addReferenceModal" class="modal">
        <div class="modal-content">
            <h3 class="mb-4">Add Reference Slip</h3>
            <form id="addReferenceForm">
                <div class="form-group">
                    <label for="borrowerIds">Borrower:</label>
                    <select class="form-select" id="borrowerIds" name="borrowerIds" required>
                        <option value="">Select Borrower</option>
                        <?php
                        $borrowersQuery = "SELECT idNumber, firstName, surName FROM tblborrowers ORDER BY surName, firstName";
                        $borrowersResult = $conn->query($borrowersQuery);
                        while ($borrower = $borrowersResult->fetch_assoc()): ?>
                            <option value="<?= $borrower['idNumber']; ?>">
                                <?= $borrower['idNumber'] . ' - ' . $borrower['surName'] . ', ' . $borrower['firstName']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="type">Type:</label>
                    <select class="form-select" id="type" name="type" onchange="toggleOtherType()" required>
                        <option value="Book">Book</option>
                        <option value="Periodicals">Periodicals</option>
                        <option value="Thesis/Dissertation">Thesis/Dissertation</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                
                <div class="form-group" id="otherTypeContainer" style="display: none;">
                    <label for="otherType">Specify Type:</label>
                    <input type="text" class="form-control" id="otherType" name="otherType">
                </div>
                
                <div class="form-group">
                    <label for="author">Author:</label>
                    <select class="form-select" id="author" name="author" onchange="fetchBooksByAuthor()" required>
                        <option value="">Select an Author</option>
                        <?php while ($author = $authorsResult->fetch_assoc()): ?>
                            <option value="<?= $author['authorId']; ?>"><?= htmlspecialchars($author['authorName']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Title:</label>
                    <select class="form-select" id="title" name="title" onchange="fetchBookDetails()" required>
                        <option value="">Select a Title</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category">Subject:</label>
                    <input type="text" class="form-control" id="category" name="category" readonly>
                </div>

                <div class="form-group">
                    <label for="callNumber">Call Number:</label>
                    <input type="text" class="form-control" id="callNumber" name="callNumber" readonly>
                </div>

                <div class="form-group">
                    <label for="subLocation">SubLocation:</label>
                    <input type="text" class="form-control" id="subLocation" name="subLocation" required>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" onclick="closeModal('addReferenceModal')" class="btn btn-danger">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- MDB JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script>

        function fetchBooksByAuthor() {
            let authorId = document.getElementById("author").value;
            let titleSelect = document.getElementById("title");
            titleSelect.innerHTML = '<option value="">Select a Title</option>';

            if (authorId) {
                $.ajax({
                    url: 'process/get-books.php',
                    type: 'POST',
                    data: { authorId: authorId },
                    dataType: 'json',
                    success: function (books) {
                        books.forEach(book => {
                            let option = document.createElement("option");
                            option.value = book.bookId;
                            option.textContent = book.title;
                            titleSelect.appendChild(option);
                        });
                    }
                });
            }
        }

        function fetchBookDetails() {
            let bookId = document.getElementById("title").value;
            let categoryInput = document.getElementById("category");
            let callNumberInput = document.getElementById("callNumber");
            categoryInput.value = "";
            callNumberInput.value = "";

            if (bookId) {
                $.ajax({
                    url: 'process/get-book-details.php',
                    type: 'POST',
                    data: { bookId: bookId },
                    dataType: 'json',
                    success: function (data) {
                        categoryInput.value = data.category;
                        callNumberInput.value = data.callNumber;
                    }
                });
            }
        }

        function toggleOtherType() {
            var typeSelect = document.getElementById("type");
            var otherTypeContainer = document.getElementById("otherTypeContainer");
            
            if (typeSelect.value === "Others") {
                otherTypeContainer.style.display = "block";
            } else {
                otherTypeContainer.style.display = "none";
            }
        }

        function editReferenceSlip(referenceId) {
            window.location.href = "edit-reference.php?referenceId=" + referenceId;
        }

        $(document).ready(function() {
            $('#dataTable').DataTable();
            $(document).ready(function() {
            $('#openModalBtn').on('click', function() {
                $('#approveModal').modal('show');
            });

            $('#approveBtn').on('click', function() {
                $('#approveModal').modal('hide');
            });
           });

            $('.delete-btn').on('click', function() {
                const referenceId = $(this).data('reference-id');
                if (confirm('Are you sure you want to delete this reference slip?')) {
                    $.ajax({
                        url: 'process/delete-reference-slip.php',
                        type: 'POST',
                        data: { referenceId },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert('Error deleting reference slip. Please try again.');
                        }
                    });
                }
            });
        });

        function printReferenceSlip(referenceId) {
            window.location.href = "pdf/generate-ref.php?referenceId=" + referenceId;
        }


        function showAddReferenceModal() {
            document.getElementById('addReferenceModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('addReferenceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const borrowerId = document.getElementById('borrowerIds').value;
            let type = document.getElementById('type').value;
            const otherTypeInput = document.getElementById('otherType'); // Get the otherType input field
            const author = document.getElementById('author').value;
            const title = document.getElementById('title').value;
            const category = document.getElementById('category').value;
            const callNumber = document.getElementById('callNumber').value;
            const subLocation = document.getElementById('subLocation').value;
            const date = document.getElementById('date').value;

            // If "Others" is selected and the user has inputted a value, use that value instead
            if (type === "Others" && otherTypeInput && otherTypeInput.value.trim() !== "") {
                type = otherTypeInput.value.trim();
            }

            $.ajax({
                url: 'process/add-reference-slip.php',
                type: 'POST',
                data: { borrowerId, type, author, title, category, callNumber, subLocation, date },
                success: function(response) {
                    alert(response);
                    closeModal('addReferenceModal');
                    location.reload();
                },
                error: function() {
                    alert('Error adding reference slip. Please try again.');
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
