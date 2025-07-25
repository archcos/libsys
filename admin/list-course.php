<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php'); // Redirect to login if not logged in
    exit;
}

include('process/db-connect.php'); // Database connection
ob_start();

// Fetch courses
$query = "SELECT courseId, courseName, level FROM tblcourses ORDER BY courseName ASC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program List</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
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
        .delete-btn {
            background-color: lightcoral;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Program List</h6>
    </div>
    <div class="card-body">
        
        <button class="btn btn-primary" onclick="openAddModal()">Add New Program</button>
        <div style="margin-top: 20px;">
        </div>

        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Program ID</th>
                    <th>Level</th>
                    <th>Program Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['courseId'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['level']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
                        echo "<td>
                                <button class='btn btn-primary' onclick='openEditModal(" . $row['courseId'] . ", \"" . addslashes($row['courseName']) . "\", \"" . $row['level'] . "\")'>Edit</button>
                            </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Add/Edit Modal -->
    <div id="courseModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background-color:white; padding:20px; border-radius:10px; box-shadow:0px 0px 10px rgba(0,0,0,0.3);">
        <h2 id="modalTitle">Add Program</h2>
        <form id="courseForm">
            <input type="hidden" id="courseId">
            <label for="level">Level:</label><br>
            <select id="level" name="level" required>
                <option value="Undergraduate">Undergraduate</option>
                <option value="Postgraduate">Postgraduate</option>
                <option value="Doctoral">Doctoral</option>
            </select><br><br>
            <label for="courseName">Program Name:</label><br>
            <input type="text" id="courseName" name="courseName" required><br><br>
            <button type="submit" class="btn add-btn">Save</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        </form>
    </div>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        // Open Add Modal
        function openAddModal() {
            $('#courseId').val('');
            $('#courseName').val('');
            $('#level').val('Undergraduate');
            $('#modalTitle').text('Add Program');
            $('#courseModal').show();
        }

        // Open Edit Modal
        function openEditModal(courseId, courseName, level) {
            $('#courseId').val(courseId);
            $('#courseName').val(courseName);
            $('#level').val(level);
            $('#modalTitle').text('Edit Program');
            $('#courseModal').show();
        }

        // Close Modal
        function closeModal() {
            $('#courseModal').hide();
        }

        // Save Course (Add/Edit)
        $('#courseForm').on('submit', function(e) {
            e.preventDefault();
            const courseId = $('#courseId').val();
            const courseName = $('#courseName').val();
            const level = $('#level').val();

            $.ajax({
                url: courseId ? 'process/edit-course.php' : 'process/add-course.php',
                type: 'POST',
                data: { courseId, courseName, level },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Error saving course.');
                }
            });
        });

        // Delete Course
        function deleteCourse(courseId) {
            if (confirm('Are you sure you want to delete this course?')) {
                $.ajax({
                    url: 'process/delete-course.php',
                    type: 'POST',
                    data: { courseId },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert('Error deleting course.');
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
