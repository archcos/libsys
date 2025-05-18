<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: sign-in.php');  // Change 'login.php' to your login page
    exit;  // Make sure the script stops executing after the redirect
}

// Include your database connection file
include('process/db-connect.php'); // Adjust the path as needed
ob_start();

// Fetch categories and authors from the database
$authorsQuery = "SELECT * FROM tblauthor";
$authorsResult = $conn->query($authorsQuery);

$categoriesQuery = "SELECT * FROM tblcategory";
$categoriesResult = $conn->query($categoriesQuery);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $edition = trim($_POST['edition']);
    $authorIds = $_POST['authors'] ?? [];
    $categoryIds = $_POST['categories'] ?? [];
    $quantity = trim($_POST['quantity']);
    $callNum = trim($_POST['callNum']);
    $accessionNum = trim($_POST['accessionNum']);
    $barcodeNum = trim($_POST['barcodeNum']);
    $publisher = trim($_POST['publisher']);
    $publishedDate = trim($_POST['publishedDate']);

    if (empty($title)) {
        $error = "Title is required!";
    } else if (empty($accessionNum)) {
        $error = "Accession Number is required!";
    } else if (empty($publisher)) {
        $error = "Publisher is required!";
    } else if (empty($publishedDate)) {
        $error = "Copyright Year is required!";
    } else if (empty($quantity)) {
        $error = "Volume is required!";
    } else {
        $dateAdded = date('Y-m-d');
        $finalAuthorId = null;
        $finalCategoryId = null;

        // Handle Authors if selected
        if (!empty($authorIds)) {
            if (count($authorIds) > 1) {
                $authorNames = [];
                foreach ($authorIds as $aid) {
                    $res = $conn->query("SELECT CONCAT(firstName, ' ', lastName) as fullName FROM tblauthor WHERE authorId = $aid");
                    if ($row = $res->fetch_assoc()) {
                        $authorNames[] = $row['fullName'];
                    }
                }
                $combinedAuthor = implode(', ', $authorNames);

                // Save combined author to tblauthor
                $stmtAuthorInsert = $conn->prepare("INSERT INTO tblauthor (firstName, lastName) VALUES (?, '')");
                $stmtAuthorInsert->bind_param("s", $combinedAuthor);
                $stmtAuthorInsert->execute();
                $finalAuthorId = $stmtAuthorInsert->insert_id;
                $stmtAuthorInsert->close();
            } else if (count($authorIds) == 1) {
                $finalAuthorId = intval($authorIds[0]);
            }
        }

        // Handle Categories if selected
        if (!empty($categoryIds)) {
            if (count($categoryIds) > 1) {
                $categoryNames = [];
                foreach ($categoryIds as $cid) {
                    $res = $conn->query("SELECT categoryName FROM tblcategory WHERE categoryId = $cid");
                    if ($row = $res->fetch_assoc()) {
                        $categoryNames[] = $row['categoryName'];
                    }
                }
                $combinedCategory = implode(', ', $categoryNames);

                // Save combined category to tblcategory
                $stmtCategoryInsert = $conn->prepare("INSERT INTO tblcategory (categoryName) VALUES (?)");
                $stmtCategoryInsert->bind_param("s", $combinedCategory);
                $stmtCategoryInsert->execute();
                $finalCategoryId = $stmtCategoryInsert->insert_id;
                $stmtCategoryInsert->close();
            } else if (count($categoryIds) == 1) {
                $finalCategoryId = intval($categoryIds[0]);
            }
        }

        // Insert book with optional authorId and categoryId
        $query = "INSERT INTO tblbooks (title, edition, authorId, categoryId, dateAdded, quantity, callNum, accessionNum, barcodeNum, publisher, publishedDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssiisisssss", $title, $edition, $finalAuthorId, $finalCategoryId, $dateAdded, $quantity, $callNum, $accessionNum, $barcodeNum, $publisher, $publishedDate);

        if ($stmt->execute()) {
            $success = "Book added successfully!";
        } else {
            $error = "Error: Could not add the book. " . $conn->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .addbtn {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center;    /* Center vertically */    
        }
        
        .addbtn:hover {
            background: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Book</h1>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter book title" required>
            </div>

            <div class="form-group">
                <label for="edition">Edition:</label>
                <input type="text" id="edition" name="edition" placeholder="Enter Edition (Optional)">
            </div>
            
            <div class="form-group">
                <label for="author">Author (Optional):</label>
                <div class="dropdown mb-2">
                    <button type="button" onclick="toggleDropdown()" class="btn btn-light border" style="width: 100%;">Select Author(s)</button>
                    <div id="checkboxDropdown" class="dropdown-content border p-2" style="display: none; max-height: 200px; overflow-y: auto;">
                        <?php while ($author = $authorsResult->fetch_assoc()): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="authors[]" value="<?php echo $author['authorId']; ?>" id="author_<?php echo $author['authorId']; ?>">
                                <label class="form-check-label" for="author_<?php echo $author['authorId']; ?>">
                                    <?php echo $author['firstName'] . ' ' . $author['lastName']; ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <!-- New Author Input Fields -->
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" onclick="toggleNewAuthorFields()">+ Add New Author</button>
                </div>
                <div id="newAuthorFields" style="display: none;">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="newAuthorFirstName" placeholder="First Name">
                        <input type="text" class="form-control" id="newAuthorLastName" placeholder="Last Name (Required)">
                        <button type="button" class="btn btn-success" onclick="addNewAuthor()">Add</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="category">Subject (Optional):</label>
                <div class="dropdown mb-2">
                    <button type="button" onclick="toggleDropdown2()" class="btn btn-light border" style="width: 100%;">Select Category(ies)</button>
                    <div id="checkboxDropdown2" class="dropdown-content border p-2" style="display: none; max-height: 200px; overflow-y: auto;">
                        <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['categoryId']; ?>">
                                <label class="form-check-label" for="category_<?php echo $category['categoryId']; ?>">
                                    <?php echo $category['categoryName']; ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <!-- New Subject Input Field -->
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" onclick="toggleNewSubjectField()">+ Add New Subject</button>
                </div>
                <div id="newSubjectField" style="display: none;">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="newSubjectName" placeholder="Subject Name">
                        <button type="button" class="btn btn-success" onclick="addNewSubject()">Add</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="callNum">Call Number (Optional):</label>
                <input type="text" id="callNum" name="callNum" placeholder="Enter Call Number">
            </div>
            <div class="form-group">
                <label for="accessionNum">Accession Number:</label>
                <input type="text" id="accessionNum" name="accessionNum" placeholder="Enter Accession Number" required>
            </div>
            <div class="form-group">
                <label for="barcodeNum">Barcode Number (Optional):</label>
                <input type="text" id="barcodeNum" name="barcodeNum" placeholder="Enter Barcode Number">
            </div>
            <div class="form-group">
                <label for="publisher">Publisher:</label>
                <input type="text" id="publisher" name="publisher" placeholder="Enter Publisher" required>
            </div>
            <div class="form-group">
                <label for="publishedDate">Copyright:</label>
                <input type="date" id="publishedDate" name="publishedDate" placeholder="Enter Published Date" required>
            </div>
            <div class="form-group">
                <label for="quantity">Volume:</label>
                <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>
</body>
</html>
<script>
function toggleDropdown() {
    var dropdown = document.getElementById("checkboxDropdown");
    dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
}

function toggleDropdown2() {
    var dropdown = document.getElementById("checkboxDropdown2");
    dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
}

document.addEventListener('click', function(event) {
    var dropdown = document.getElementById("checkboxDropdown");
    var button = event.target.closest('.dropdown');
    if (!button) {
        dropdown.style.display = "none";
    }
});

document.addEventListener('click', function(event) {
    var dropdown = document.getElementById("checkboxDropdown2");
    var button = event.target.closest('.dropdown');
    if (!button) {
        dropdown.style.display = "none";
    }
});

        function toggleNewAuthorFields() {
            const fields = document.getElementById('newAuthorFields');
            fields.style.display = fields.style.display === 'none' ? 'block' : 'none';
        }

        function toggleNewSubjectField() {
            const field = document.getElementById('newSubjectField');
            field.style.display = field.style.display === 'none' ? 'block' : 'none';
        }

        function addNewAuthor() {
            const firstName = document.getElementById('newAuthorFirstName').value.trim();
            const lastName = document.getElementById('newAuthorLastName').value.trim();

            if (!lastName) {
                alert('Last Name is required!');
                return;
            }

            // Send AJAX request to add new author
            $.ajax({
                url: 'process/add-author-ajax.php',
                type: 'POST',
                data: {
                    firstName: firstName,
                    lastName: lastName
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Add new checkbox to the dropdown
                        const dropdown = document.getElementById('checkboxDropdown');
                        const div = document.createElement('div');
                        div.className = 'form-check';
                        div.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="authors[]" value="${result.authorId}" id="author_${result.authorId}" checked>
                            <label class="form-check-label" for="author_${result.authorId}">
                                ${firstName} ${lastName}
                            </label>
                        `;
                        dropdown.appendChild(div);

                        // Clear input fields
                        document.getElementById('newAuthorFirstName').value = '';
                        document.getElementById('newAuthorLastName').value = '';
                        toggleNewAuthorFields();
                        alert('Author added successfully!');
                    } else {
                        alert('Error adding author: ' + result.message);
                    }
                },
                error: function() {
                    alert('Error adding author. Please try again.');
                }
            });
        }

        function addNewSubject() {
            const subjectName = document.getElementById('newSubjectName').value.trim();

            if (!subjectName) {
                alert('Subject name is required!');
                return;
            }

            // Send AJAX request to add new subject
            $.ajax({
                url: 'process/add-subject-ajax.php',
                type: 'POST',
                data: {
                    categoryName: subjectName
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Add new checkbox to the dropdown
                        const dropdown = document.getElementById('checkboxDropdown2');
                        const div = document.createElement('div');
                        div.className = 'form-check';
                        div.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="categories[]" value="${result.categoryId}" checked>
                            <label class="form-check-label" for="category_${result.categoryId}">
                                ${subjectName}
                            </label>
                        `;
                        dropdown.appendChild(div);

                        // Clear input field
                        document.getElementById('newSubjectName').value = '';
                        toggleNewSubjectField();
                        alert('Subject added successfully!');
                    } else {
                        alert('Error adding subject: ' + result.message);
                    }
                },
                error: function() {
                    alert('Error adding subject. Please try again.');
                }
            });
        }
</script>
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
