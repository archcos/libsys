<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

ob_start();

include('process/db-connect.php'); // Adjust the path to your actual connection file

// Fetch all categories
$query = "SELECT * FROM tblcategory";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
        .edit-btn {
            background-color: blue;
        }
        .edit-btn:hover {
            background-color: darkblue;
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
            background: rgba(0, 0, 0, 0.5);
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .modal-content form {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Categories</h1>
        <button class="btn btn-primary" onclick="showModal('addCategoryModal')">Add New Category</button>
        <div style="margin-top: 20px;">  
</div>
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                // Display categories with clickable links
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['categoryId'] . "</td>";
                        echo "<td>
                                <a href='list-books.php?categoryId=" . $row['categoryId'] . "'>
                                    " . htmlspecialchars($row['categoryName']) . "
                                </a>
                            </td>";
                        echo "<td>
                                <button class='btn edit-btn' data-category-id='" . $row['categoryId'] . "' data-category-name='" . htmlspecialchars($row['categoryName']) . "'>Edit</button>
                                <button class='btn delete-btn' data-category-id='" . $row['categoryId'] . "'>Delete</button>
                            </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <h3>Add Category</h3>
            <form id="addCategoryForm">
                <label for="categoryName">Category Name:</label><br>
                <input type="text" id="categoryName" name="categoryName" required><br><br>
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" onclick="closeModal('addCategoryModal')" class="btn cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Books List Modal -->
    <div id="booksListModal" class="modal">
        <div class="modal-content">
            <h3>Books in Selected Category</h3>
            <table border="1" id="booksTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Filtered books will appear here -->
                </tbody>
            </table>
            <button onclick="closeModal('booksListModal')" class="btn delete-btn">Close</button>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <h3>Edit Category</h3>
            <form id="editCategoryForm">
                <input type="hidden" id="editCategoryId" name="categoryId">
                <label for="editCategoryName">Category Name:</label><br>
                <input type="text" id="editCategoryName" name="categoryName" required><br><br>
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" onclick="closeModal('editCategoryModal')" class="btn cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>

        $(document).ready(function() {
                $('#dataTable').DataTable();

                // Open edit modal and populate fields
                $('.edit-btn').on('click', function() {
                    let categoryId = $(this).data('category-id');
                    let categoryName = $(this).data('category-name');

                    $('#editCategoryId').val(categoryId);
                    $('#editCategoryName').val(categoryName);

                    showModal('editCategoryModal');
                });

                // Handle category update form submission
                $('#editCategoryForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: 'process/edit-category.php', // Backend script to handle editing
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            alert(response);
                            closeModal('editCategoryModal');
                            location.reload();
                        },
                        error: function() {
                            alert('Error updating category. Please try again.');
                        }
                    });
                });
            });

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
                const categoryId = $(this).data('category-id');
                if (confirm('Are you sure you want to delete this category?')) {
                    $.ajax({
                        url: 'process/delete-category.php', // Backend script to handle delete
                        type: 'POST',
                        data: { categoryId },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert('Error deleting category. Please try again.');
                        }
                    });
                }
            });

            // Handle filter by category click
            $('.filter-category').on('click', function(e) {
                e.preventDefault();
                const categoryId = $(this).data('category-id');
                $.ajax({
                    url: 'process/filter-books.php', // Backend script to filter books
                    type: 'GET',
                    data: { categoryId },
                    success: function(response) {
                        $('#booksTable tbody').html(response);
                        showModal('booksListModal');
                    },
                    error: function() {
                        alert('Error fetching books. Please try again.');
                    }
                });
            });
        });

        // Show modal
        function showModal(modalId) {
            let modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeModal(modalId) {
            let modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }


        // Handle Add Category Form Submission
        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const categoryName = document.getElementById('categoryName').value;

            $.ajax({
                url: 'process/add-category.php', // Backend script to handle add
                type: 'POST',
                data: { categoryName },
                success: function(response) {
                    alert(response);
                    closeModal('addCategoryModal');
                    location.reload();
                },
                error: function() {
                    alert('Error adding category. Please try again.');
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
