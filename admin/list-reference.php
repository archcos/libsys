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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
        .delete-btn {
            background-color: red;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        .cancel-btn {
            background-color: red;
        }
        .cancel-btn:hover {
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
    </style>
</head>

<?php
// Fetch authors with full name (firstName + lastName)
$authorsQuery = "SELECT authorId, CONCAT(firstName, ' ', lastName) AS authorName FROM tblauthor";
$authorsResult = $conn->query($authorsQuery);
?>

<body>
<div class="container">
        <h1>Manage Reference Slips</h1>
        <button class="btn add-btn" onclick="showAddReferenceModal()">Add New Reference Slip</button>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Print</th>
                    <th>Reference ID</th>
                    <th>Borrower Name</th>
                    <th>Author</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Type</th>
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
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>
                                    <button class='btn delete-btn' data-reference-id='" . $row['referenceId'] . "'>Delete</button>
                                </td>";
                            echo "</tr>"; // Ensure the row closes properly
                        }
                    }
                ?>
            </tbody>

        </table>
    </div>

    <!-- Add Reference Slip Modal -->
    <div id="addReferenceModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Add Reference Slip</h3>
            <form id="addReferenceForm">
                <label for="borrowerIds">Borrower ID:</label><br>
                <input type="number" id="borrowerIds" name="borrowerIds" required><br><br>
                
                <label for="type">Type:</label><br>
                <select id="type" name="type" onchange="toggleOtherType()" required>
                    <option value="Book">Book</option>
                    <option value="Periodicals">Periodicals</option>
                    <option value="Thesis/Dissertation">Thesis/Dissertation</option>
                    <option value="Others">Others</option>
                </select><br><br>
                
                <div id="otherTypeContainer" style="display: none;">
                    <label for="otherType">Specify Type:</label><br>
                    <input type="text" id="otherType" name="otherType"><br><br>
                </div>
                
                <label for="author">Author:</label><br>
                <select id="author" name="author" onchange="fetchBooksByAuthor()" required>
                    <option value="">Select an Author</option>
                    <?php while ($author = $authorsResult->fetch_assoc()): ?>
                        <option value="<?= $author['authorId']; ?>"><?= htmlspecialchars($author['authorName']); ?></option>
                    <?php endwhile; ?>
                </select><br><br>

                <label for="title">Title:</label><br>
                <select id="title" name="title" onchange="fetchCategoryByBook()" required>
                    <option value="">Select a Title</option>
                </select><br><br>

                <label for="category">Category:</label><br>
                <input type="text" id="category" name="category" readonly><br><br>

                <label for="date">Date:</label><br>
                <input type="date" id="date" name="date" required><br><br>
                <button type="submit" class="btn add-btn">Add</button>
                <button type="button" onclick="closeModal('addReferenceModal')" class="btn cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>

        function fetchBooksByAuthor() {
            let authorId = document.getElementById("author").value;
            let titleSelect = document.getElementById("title");
            titleSelect.innerHTML = '<option value="">Select a Title</option>'; // Reset title dropdown

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

        function fetchCategoryByBook() {
            let bookId = document.getElementById("title").value;
            let categoryInput = document.getElementById("category");
            categoryInput.value = ""; // Reset category input

            if (bookId) {
                $.ajax({
                    url: 'process/get-category.php',
                    type: 'POST',
                    data: { bookId: bookId },
                    dataType: 'json',
                    success: function (data) {
                        categoryInput.value = data.category;
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
            const date = document.getElementById('date').value;

            // If "Others" is selected and the user has inputted a value, use that value instead
            if (type === "Others" && otherTypeInput && otherTypeInput.value.trim() !== "") {
                type = otherTypeInput.value.trim();
            }

            $.ajax({
                url: 'process/add-reference-slip.php',
                type: 'POST',
                data: { borrowerId, type, author, title, category, date },
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
