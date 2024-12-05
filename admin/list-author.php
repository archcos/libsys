<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

include('process/db-connect.php');
ob_start();

// Fetch all authors
$query = "SELECT * FROM tblauthor";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authors</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Authors</h1>
        <button class="btn add-btn" onclick="showAddAuthorModal()">Add New Author</button>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Author ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                        echo "<tr>";
                        echo "<td>" . $row['authorId'] . "</td>";
                        echo "<td>
                                <a href='list-books.php?authorId=" . $row['authorId'] . "'>
                                    " . $fullName . "
                                </a>
                            </td>";
                        echo "<td>
                                <button class='btn delete-btn' data-author-id='" . $row['authorId'] . "'>Delete</button>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No authors found</td></tr>";
                }
            ?>

            </tbody>
        </table>
    </div>

    <!-- Add Author Modal -->
    <div id="addAuthorModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Add Author</h3>
            <form id="addAuthorForm">
                <label for="firstName">First Name:</label><br>
                <input type="text" id="firstName" name="firstName" required><br><br>
                <label for="lastName">Last Name:</label><br>
                <input type="text" id="lastName" name="lastName" required><br><br>
                <button type="submit" class="btn add-btn">Add</button>
                <button type="button" onclick="closeModal('addAuthorModal')" class="btn cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();

            $('.delete-btn').on('click', function() {
                const authorId = $(this).data('author-id');
                if (confirm('Are you sure you want to delete this author?')) {
                    $.ajax({
                        url: 'process/delete-author.php',
                        type: 'POST',
                        data: { authorId },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert('Error deleting author. Please try again.');
                        }
                    });
                }
            });
        });

        function showAddAuthorModal() {
            document.getElementById('addAuthorModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('addAuthorForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;

            $.ajax({
                url: 'process/add-author.php',
                type: 'POST',
                data: { firstName, lastName },
                success: function(response) {
                    alert(response);
                    closeModal('addAuthorModal');
                    location.reload();
                },
                error: function() {
                    alert('Error adding author. Please try again.');
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
