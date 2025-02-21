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
    $authorId = trim($_POST['author']);
    $categoryId = trim($_POST['category']);
    $quantity = trim($_POST['quantity']);
    $callNum = trim($_POST['callNum']);
    $accessionNum = trim($_POST['accessionNum']);
    $barcodeNum = trim($_POST['barcodeNum']);
    $publisher = trim($_POST['publisher']);
    $publishedDate = trim($_POST['publishedDate']);


    // Validate inputs
    if (empty($title) || empty($authorId) || empty($categoryId)) {
        $error = "All fields are required!";
    } else {
        // Insert the data into the database
        $query = "INSERT INTO tblbooks (title, authorId, categoryId, dateAdded, quantity, callNum, accessionNum, barcodeNum, publisher, publishedDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssisssss", $title, $authorId, $categoryId, $dateAdded, $quantity, $callNum, $accessionNum, $barcodeNum, $publisher, $publishedDate);

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
            
            <!-- Author Select Dropdown -->
            <div class="form-group">
                <label for="author">Author:</label>
                <select id="author" name="author" required>
                    <option value="">Select Author</option>
                    <?php while ($author = $authorsResult->fetch_assoc()): ?>
                        <option value="<?php echo $author['authorId']; ?>"><?php echo $author['firstName'] . ' ' . $author['lastName']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Category Select Dropdown -->
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                        <option value="<?php echo $category['categoryId']; ?>"><?php echo $category['categoryName']; ?></option>
                    <?php endwhile; ?>
                </select>
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
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
