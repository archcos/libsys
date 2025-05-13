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

    if (empty($title) || empty($authorIds) || empty($categoryIds)) {
        $error = "All fields are required!";
    } else {
        $dateAdded = date('Y-m-d');

        // Handle Authors
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
        } else {
            $finalAuthorId = intval($authorIds[0]);
        }

        // Handle Categories
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
        } else {
            $finalCategoryId = intval($categoryIds[0]);
        }

        // Insert book with finalAuthorId and finalCategoryId
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
                <input type="text" id="edition" name="edition" placeholder="Enter Edition">
            </div>
            
            <div class="form-group">
                <label for="author">Author:
                    <a href="list-author.php" target="_blank" style="font-size: 0.9em; margin-left: 10px;">+ Add Authors</a>
                </label>
                <div class="dropdown">
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
            </div>

            <div class="form-group">
                <label for="category">Category:
                    <a href="list-category.php" target="_blank" style="font-size: 0.9em; margin-left: 10px;">+ Add Categories</a>
                </label>
                <div class="dropdown">
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
            </div>

            <div class="form-group">
                <label for="callNum">Call Number:</label>
                <input type="text" id="callNum" name="callNum" placeholder="Enter Call Number" required>
            </div>
            <div class="form-group">
                <label for="accessionNum">Accession Number:</label>
                <input type="text" id="accessionNum" name="accessionNum" placeholder="Enter Accession Number" required>
            </div>
            <div class="form-group">
                <label for="barcodeNum">Barcode Number:</label>
                <input type="text" id="barcodeNum" name="barcodeNum" placeholder="Enter Barcode Number" required>
            </div>
            <div class="form-group">
                <label for="publisher">Publisher:</label>
                <input type="text" id="publisher" name="publisher" placeholder="Enter Publisher" required>
            </div>
            <div class="form-group">
                <label for="publishedDate">Published Date:</label>
                <input type="date" id="publishedDate" name="publishedDate" placeholder="Enter Published Date" required>
            </div>
            <!-- Quantity Field -->
            <div class="form-group">
                <label for="quantity">Quantity:</label>
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
</script>
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
