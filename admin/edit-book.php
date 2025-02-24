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
    $title = trim($_POST['title']);
    $authorId = trim($_POST['author']);
    $categoryId = trim($_POST['category']);
    $quantity = trim($_POST['quantity']);
    $callNum = trim($_POST['callNum']);
    $accessionNum = trim($_POST['accessionNum']);
    $barcodeNum = trim($_POST['barcodeNum']);
    $publisher = trim($_POST['publisher']);
    $publishedDate = trim($_POST['publishedDate']);

    // Validate input
    if (empty($title) || empty($authorId) || empty($categoryId)) {
        $error = "All fields are required!";
    } else {
        $updateQuery = "UPDATE tblbooks 
                        SET title = ?, authorId = ?, categoryId = ?, quantity = ?, callNum = ?, accessionNum = ?, barcodeNum = ?, publisher = ?, publishedDate = ?
                        WHERE bookId = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("siissssssi", $title, $authorId, $categoryId, $quantity, $callNum, $accessionNum, $barcodeNum, $publisher, $publishedDate, $bookId);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
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
        }
        .form-group {
            margin-bottom: 15px;
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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="author">Author:</label>
                <select id="author" name="author" required>
                    <?php while ($author = $authorsResult->fetch_assoc()): ?>
                        <option value="<?php echo $author['authorId']; ?>" <?php echo ($author['authorId'] == $book['authorId']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['firstName'] . ' ' . $author['lastName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo $category['categoryId']; ?>" <?php echo ($category['categoryId'] == $book['categoryId']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['categoryName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" required>
            </div>

            <div class="form-group">
                <label for="callNum">Call Number:</label>
                <input type="text" id="callNum" name="callNum" value="<?php echo htmlspecialchars($book['callNum']); ?>" required>
            </div>

            <div class="form-group">
                <label for="accessionNum">Accession Number:</label>
                <input type="text" id="accessionNum" name="accessionNum" value="<?php echo htmlspecialchars($book['accessionNum']); ?>" required>
            </div>

            <div class="form-group">
                <label for="barcodeNum">Barcode Number:</label>
                <input type="text" id="barcodeNum" name="barcodeNum" value="<?php echo htmlspecialchars($book['barcodeNum']); ?>" required>
            </div>

            <div class="form-group">
                <label for="publisher">Publisher:</label>
                <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>" required>
            </div>

            <div class="form-group">
                <label for="publishedDate">Published Date:</label>
                <input type="date" id="publishedDate" name="publishedDate" value="<?php echo htmlspecialchars($book['publishedDate']); ?>" required>
            </div>

            <button type="submit" class="submit-btn">Update Book</button>
        </form>
    </div>
</body>
</html>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
