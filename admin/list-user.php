<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

include('process/db-connect.php');
ob_start();

// Fetch all users
$query = "SELECT * FROM tbluser";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Administrators</title>
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
            background-color: green;
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
    </style>
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Manage Administrators</h6>
    </div>
    <div class="card-body">
        <button class="btn btn-primary" onclick="showAddUserModal()">Add New Admin</button>
        <div style="margin-top: 20px;"> 
            <table id="dataTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Account Type</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Last Login</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['accountType']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['lastName']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['lastLogin']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['dateCreated']) . "</td>";
                                echo "<td>
                                        <button class='btn btn-primary' data-user-id='" . $row['userId'] . "' onclick='editUser(" . $row['userId'] . ")'>Edit</button>
                                        <button class='btn delete-btn' data-user-id='" . $row['userId'] . "' onclick='deleteUser(" . $row['userId'] . ")'>Delete</button>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>    
    </div>
</div>

    <!-- Add User Modal -->
    <div id="addUserModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Add User</h3>
            <form id="addUserForm">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                <label for="firstName">First Name:</label><br>
                <input type="text" id="firstName" name="firstName" required><br><br>
                <label for="lastName">Last Name:</label><br>
                <input type="text" id="lastName" name="lastName" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <label for="accountType">Account Type:</label><br>
                <select id="accountType" name="accountType" required>
                    <option value="Admin">Admin</option>
                    <option value="Librarian">Librarian</option>
                </select><br><br>
                <button type="submit" class="btn btn-primary">Add User</button>
                <button type="button" onclick="closeModal('addUserModal')" class="btn cancel-btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" style="display: none; position: fixed; z-index: 1000; background: rgba(0, 0, 0, 0.5); top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 300px;">
            <h3>Edit User</h3>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="userId">
                <label for="editUsername">Username:</label><br>
                <input type="text" id="editUsername" name="username" required><br><br>
                <label for="editFirstName">First Name:</label><br>
                <input type="text" id="editFirstName" name="firstName" required><br><br>
                <label for="editLastName">Last Name:</label><br>
                <input type="text" id="editLastName" name="lastName" required><br><br>
                <label for="editPassword">Password:</label><br>
                <input type="password" id="editPassword" name="password"><br><br>
                <label for="editAccountType">Account Type:</label><br>
                <select id="editAccountType" name="accountType" required>
                    <option value="Admin">Admin</option>
                    <option value="Librarian">Librarian</option>
                </select>
                <br><br>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" onclick="closeModal('editUserModal')" class="btn cancel-btn">Cancel</button>
            </form>
        </div>
    </div>


    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        function showAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Add User
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const accountType = document.getElementById('accountType').value;
            const password = document.getElementById('password').value;

            $.ajax({
                url: 'admin/add-user.php',
                type: 'POST',
                data: { username, firstName, lastName, accountType, password },
                success: function(response) {
                    alert(response);
                    closeModal('addUserModal');
                    location.reload();
                },
                error: function() {
                    alert('Error adding user. Please try again.');
                }
            });
        });

        function editUser(userId) {
            // Fetch user data from the server to fill the form
            $.ajax({
                url: 'admin/get-user.php',
                type: 'POST',
                data: { userId },
                success: function(response) {
                    const data = JSON.parse(response); // Ensure the response is parsed as JSON
                    if (data.success) {
                        const user = data.user;  // Get the user data from the response

                        // Populate the form fields with the user data
                        document.getElementById('editUserId').value = user.userId;
                        document.getElementById('editUsername').value = user.username;
                        document.getElementById('editFirstName').value = user.firstName;
                        document.getElementById('editLastName').value = user.lastName;
                        document.getElementById('editPassword').value = ''; // Optionally keep the password empty
                        document.getElementById('editAccountType').value = user.accountType;

                        // Show the edit modal
                        document.getElementById('editUserModal').style.display = 'flex';
                    } else {
                        alert(data.message); // Show error message if user not found
                    }
                },
                error: function() {
                    alert('Error fetching user data. Please try again.');
                }
            });
        }

        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const userId = document.getElementById('editUserId').value;
            const username = document.getElementById('editUsername').value;
            const firstName = document.getElementById('editFirstName').value;
            const lastName = document.getElementById('editLastName').value;
            const accountType = document.getElementById('editAccountType').value;
            const password = document.getElementById('editPassword').value; // Empty password means no change

            $.ajax({
                url: 'admin/edit-user.php',
                type: 'POST',
                data: {
                    userId,
                    username,
                    firstName,
                    lastName,
                    accountType,
                    password
                },
                success: function(response) {
                    alert(response);
                    closeModal('editUserModal');
                    location.reload(); // Reload page after successful update
                },
                error: function() {
                    alert('Error editing user. Please try again.');
                }
            });
        });

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: 'admin/delete-user.php',
                    type: 'POST',
                    data: { userId },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert('Error deleting user. Please try again.');
                    }
                });
            }
        }
    </script>
</body>
</html>
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
