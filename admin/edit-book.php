<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

include('process/db-connect.php'); // Database connection
ob_start();

// Get book ID from URL
if (!isset($_GET['bookId']) || !is_numeric($_GET['bookId'])) {
    die("Invalid book ID.");
}

$bookId = intval($_GET['bookId']);

// Fetch book details
$query = "SELECT * FROM tblbooks WHERE bookId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

// Fetch authors and categories
$authorsResult = $conn->query("SELECT * FROM tblauthor");
$categoriesResult = $conn->query("SELECT * FROM tblcategory");

// Update book details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $bookId = $_POST['bookId'];
    $title = $_POST['title'];
    $edition = $_POST['edition'];
    $authorId = !empty($_POST['authorId']) ? $_POST['authorId'] : null;
    $categoryId = !empty($_POST['categoryId']) ? $_POST['categoryId'] : null;
    $quantity = $_POST['quantity'];
    $callNum = $_POST['callNum'];
    $accessionNum = $_POST['accessionNum'];
    $barcodeNum = $_POST['barcodeNum'];
    $publisher = $_POST['publisher'];
    $publishedDate = $_POST['publishedDate'];
    $status = $_POST['status'];

    // Validate input - only required fields
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
        // Prepare the SQL statement based on whether categoryId is null
        if ($categoryId === null) {
            $updateQuery = "UPDATE tblbooks 
                        SET title = ?, edition = ?, authorId = ?, categoryId = NULL, quantity = ?, callNum = ?, accessionNum = ?, barcodeNum = ?, publisher = ?, publishedDate = ?, status = ?
                        WHERE bookId = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ssisssssssi", $title, $edition, $authorId, $quantity, $callNum, $accessionNum, $barcodeNum, $publisher, $publishedDate, $status, $bookId);
        } else {
            $updateQuery = "UPDATE tblbooks 
                        SET title = ?, edition = ?, authorId = ?, categoryId = ?, quantity = ?, callNum = ?, accessionNum = ?, barcodeNum = ?, publisher = ?, publishedDate = ?, status = ?
                        WHERE bookId = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ssiisssssssi", $title, $edition, $authorId, $categoryId, $quantity, $callNum, $accessionNum, $barcodeNum, $publisher, $publishedDate, $status, $bookId);
        }

        if ($stmt->execute()) {
            $success = "Book updated successfully!";
            header("Location: list-books.php");
            exit;
        } else {
            $error = "Error updating book. " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
     <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <style>
        .container {
            max-width: 1100px;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            min-width: 180px;
            max-width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
        }
        .submit-btn {
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Book</h1>

        <?php if (!empty($success)): ?>
            <div style="color: green;"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div style="color: red;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="bookId" value="<?php echo $bookId; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="edition">Edition:</label>
                    <input type="text" id="edition" name="edition" value="<?php echo htmlspecialchars($book['edition']); ?>" placeholder="Enter Edition (Optional)">
                </div>
                <div class="form-group">
                    <label for="callNum">Call Number (Optional):</label>
                    <input type="text" id="callNum" name="callNum" value="<?php echo htmlspecialchars($book['callNum']); ?>" placeholder="Enter Call Number">
                </div>
                <div class="form-group">
                    <label for="accessionNum">Accession Number:</label>
                    <input type="text" id="accessionNum" name="accessionNum" value="<?php echo htmlspecialchars($book['accessionNum']); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="barcodeNum">Barcode Number (Optional):</label>
                    <input type="text" id="barcodeNum" name="barcodeNum" value="<?php echo htmlspecialchars($book['barcodeNum']); ?>" placeholder="Enter Barcode Number">
                </div>
                <div class="form-group">
                    <label for="publisher">Publisher:</label>
                    <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="publishedDate">Copyright Year:</label>
                    <input type="date" class="form-control" id="publishedDate" name="publishedDate" value="<?php echo htmlspecialchars($book['publishedDate']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Volume:</label>
                    <input type="number" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Active" <?php echo $book['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo $book['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="author">Author (Optional):</label>
                    <select id="author" name="authorId">
                        <option value="">Select Author</option>
                        <?php while ($author = $authorsResult->fetch_assoc()): ?>
                            <option value="<?php echo $author['authorId']; ?>" <?php echo ($author['authorId'] == $book['authorId']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($author['firstName'] . ' ' . $author['lastName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category">Subject (Optional):</label>
                    <select id="category" name="categoryId">
                        <option value="">Select Subject</option>
                        <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?php echo $category['categoryId']; ?>" <?php echo ($category['categoryId'] == $book['categoryId']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['categoryName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    </div>
</body>
</html>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
