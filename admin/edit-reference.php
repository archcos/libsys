<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

// Include database connection
include('process/db-connect.php');

// Fetch the referenceId from the URL query string
$referenceId = $_GET['referenceId'] ?? null;

if (!$referenceId) {
    echo 'No reference ID provided.';
    exit;
}

// Fetch the reference details to populate the form
$query = "SELECT rs.referenceId, 
                 rs.borrowerId, 
                 rs.type, 
                 rs.title, 
                 rs.author, 
                 rs.category, 
                 rs.callNumber, 
                 rs.subLocation, 
                 rs.date
          FROM tblreference rs
          WHERE rs.referenceId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $referenceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo 'Reference not found.';
    exit;
}

$reference = $result->fetch_assoc();

// Fetch authors to populate dropdown
$authorsQuery = "SELECT authorId, CONCAT(firstName, ' ', lastName) AS authorName FROM tblauthor";
$authorsResult = $conn->query($authorsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reference Slip</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            width: 30%;
            margin: 30px auto;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 8px 0 4px;
            font-size: 14px;
        }
        input, select, button {
            padding: 6px;
            margin: 8px 0;
            font-size: 14px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: blue;
        }
        .cancel-btn {
            background-color: lightcoral;
            margin-top: 5px;
        }
        .cancel-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Reference Slip</h1>
        <form id="editReferenceForm">
            <input type="hidden" name="referenceId" value="<?= $reference['referenceId'] ?>">

            <label for="borrowerId">Borrower ID:</label>
            <input type="number" id="borrowerId" name="borrowerId" value="<?= $reference['borrowerId'] ?>" required>

            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="Book" <?= $reference['type'] == 'Book' ? 'selected' : '' ?>>Book</option>
                <option value="Periodicals" <?= $reference['type'] == 'Periodicals' ? 'selected' : '' ?>>Periodicals</option>
                <option value="Thesis/Dissertation" <?= $reference['type'] == 'Thesis/Dissertation' ? 'selected' : '' ?>>Thesis/Dissertation</option>
                <option value="Others" <?= $reference['type'] == 'Others' ? 'selected' : '' ?>>Others</option>
            </select>

            <label for="author">Author:</label>
            <select id="author" name="author" required>
                <option value="">Select an Author</option>
                <?php while ($author = $authorsResult->fetch_assoc()): ?>
                    <option value="<?= $author['authorId'] ?>" <?= $reference['author'] == $author['authorId'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($author['authorName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="title">Title:</label>
            <select id="title" name="title" required>
                <option value="">Select a Title</option>
                <!-- Book options will be populated by JavaScript based on selected author -->
            </select>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?= htmlspecialchars($reference['category']) ?>" required>

            <label for="callNumber">Call Number:</label>
            <input type="text" id="callNumber" name="callNumber" value="<?= htmlspecialchars($reference['callNumber']) ?>" required>

            <label for="subLocation">SubLocation:</label>
            <input type="text" id="subLocation" name="subLocation" value="<?= htmlspecialchars($reference['subLocation']) ?>" required>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?= $reference['date'] ?>" required>

            <button type="submit" class="btn btn-primary">Update Reference</button>
            <button type="button" class="cancel-btn" onclick="window.location.href='list-reference.php'">Cancel</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // When an author is selected, load books for that author
            $('#author').change(function() {
                var authorId = $(this).val();
                if (authorId) {
                    // Fetch books for the selected author using AJAX
                    $.ajax({
                        url: 'get-books-by-author.php', // You need to create this PHP file
                        type: 'GET',
                        data: { authorId: authorId },
                        success: function(response) {
                            // Populate the books dropdown with the response (book options)
                            $('#title').html(response);
                        }
                    });
                } else {
                    // Clear the books dropdown if no author is selected
                    $('#title').html('<option value="">Select a Title</option>');
                }
            });

            // Pre-populate the books dropdown with the correct book based on the selected reference
            var initialAuthorId = $('#author').val();
            if (initialAuthorId) {
                $.ajax({
                    url: 'get-books-by-author.php',
                    type: 'GET',
                    data: { authorId: initialAuthorId },
                    success: function(response) {
                        $('#title').html(response);
                    }
                });
            }

            // Handle form submission
            $('#editReferenceForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: 'process/edit-reference-slip.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        alert(response);
                        if (response.includes("success")) {
                            window.history.back();  // Go back to the previous page on success
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred while updating the reference slip');
                    }
                });
            });
        });
    </script>
</body>
</html>
