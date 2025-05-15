<?php

// Include your database connection file
include('db/db-connect.php'); // Adjust the path to your actual connection file
ob_start();

// Fetch data from tblbooks with author, category names, and borrow status
$query = "SELECT b.bookId, b.title, b.quantity, b.publisher, b.publishedDate,
                 CONCAT(a.firstName, ' ', a.lastName) AS authorName, 
                 c.categoryName, YEAR(b.publishedDate) AS publishedYear
          FROM tblbooks b
          JOIN tblauthor a ON b.authorId = a.authorId
          JOIN tblcategory c ON b.categoryId = c.categoryId";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Kiosk</title>

    <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link id="main-css-href" rel="stylesheet" href="assets/css/style.css" />
  <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
   <!-- Material Design for Bootstrap (MDB) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
    font-family: 'Roboto', sans-serif;
    background: url('assets/img/lib.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    color: #333;
}


        .header {
            display: flex;
            justify-content: center; /* Center the header content */
            align-items: center;
            padding: 20px;
            position: relative; /* Allows positioning of the buttons */
            color: white;
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            text-align: center;
        }

        .header .btn-back {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .header .btn-back:hover {
            background-color: #5a6268;
        }

        .header .btn-login {
            position: absolute; /* Position the login button independently */
            right: 20px; /* Align it to the right side */
            top: 50%; /* Center vertically within the header */
            transform: translateY(-50%);
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .header .btn-login:hover {
            background-color: rgb(6, 57, 112);
            color: white;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: black;
            font-size: 2rem;
        }

        p {
            text-align: center;
            font-size: 1.1rem;
            color: #555;
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            padding: 12px 15px;
            font-size: 16px;
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 5px;
            border: 2px solid #007bff;
            outline: none;
            transition: border 0.3s;
        }

        .search-container input:focus {
            border-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            background-color: #fff;
        }

        .btn-action {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-action:hover {
            background-color: #0056b3;
        }

        .btn-nostock {
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            table {
                font-size: 0.9rem;
            }

            .search-container input {
                width: 100%;
            }
        }
        
        /* Book Modal Styles */
        .book-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
        }

        .book-modal-content {
            position: relative;
            background: white;
            margin: 5% auto;
            padding: 0;
            width: 70%;
            max-width: 800px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: modalFade 0.3s ease-in-out;
        }

        .book-cover {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
        }

        .book-cover p {
            color: white;
            opacity: 0.9;
            text-align: left;
            margin-left: 0;
        }

        .book-cover h2 {
            color: white;
            margin: 0;
            font-weight: bold;
            text-align: left;
        }

        .book-details {
            padding: 30px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-top: 25px;
        }

        .book-detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 16px;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            color: white;
            font-size: 28px;
            cursor: pointer;
            transition: 0.3s;
        }

        .close-modal:hover {
            color: #ddd;
        }

        .book-status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 1em;
            font-weight: bold;
            margin-top: 15px;
        }

        .status-available {
            background-color: #28a745;
            color: white;
        }

        .status-borrowed {
            background-color: #dc3545;
            color: white;
        }

        @keyframes modalFade {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .btn-view {
            padding: 4px 8px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }

        .btn-view:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        <a href="Kiosk.php" class="btn-back">
        <i class="fas fa-arrow-left"></i>  </a>
            <h1>Library Kiosk</h1><br>
            <a href="login.php" class="btn-login">Login</a>
        </div>

        <p>Explore the list of books available for borrowing in the library. Search, select, and borrow your favorite books.</p>

        <!-- Search Box -->
        <div class="search-container">
            <input type="text" id="searchBox" placeholder="Search books by title, author, or category..." onkeyup="searchBooks()">
        </div>

        <!-- Books Table -->
        <table id="dataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Available</th>
                    <th>Stocks</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>APA</th>
                    <th>Category</th>
                    <th>Publisher</th>
                    <th>Copyright</th>
                </tr>
            </thead>
            <tbody id="bookList">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        
                        // Borrow button or unavailable
                        echo "<td>";
                        if ($row['quantity'] > 0) {
                            echo "<button class='btn-action' onclick='handleBorrow(" . $row['bookId'] . ")'>Borrow</button>";
                        } else {
                            echo "<button class='btn-nostock btn-light' disabled>Unavailable</button>";
                        }
                        echo "</td>";

                        // Book details
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['bookId'] . "</td>";
                        echo "<td><em>" . htmlspecialchars($row['title']) . "</em> <button class='btn-view' onclick='showBookModal(" . json_encode($row) . ")'><i class='fas fa-eye'></i></button></td>";
                        echo "<td>" . htmlspecialchars($row['authorName']) . "</td>";
                        echo "<td>";
                        if ($row['authorName'] == 'No Author') {
                            echo "<em>" . htmlspecialchars($row['title']) . "</em>. (" . htmlspecialchars($row['publishedYear']) . "). " . htmlspecialchars($row['publisher']);
                        } else {
                            echo htmlspecialchars($row['authorName']) . " (" . htmlspecialchars($row['publishedYear']) . "). <em>" . htmlspecialchars($row['title']) . "</em>. " . htmlspecialchars($row['publisher']);
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['categoryName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['publisher']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['publishedDate']) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        function handleBorrow(bookId) {
            const isLoggedIn = false;
            if (!isLoggedIn) {
                window.location.href = "login.php";
                return;
            }
            currentBookId = bookId;
        }

        function searchBooks() {
            const query = document.getElementById('searchBox').value.toLowerCase();
            const rows = document.getElementById('bookList').getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = rows[i].innerText.toLowerCase().includes(query) ? '' : 'none';
            }
        }
    </script>

    <!-- Book Modal -->
    <div id="bookModal" class="book-modal">
        <div class="book-modal-content">
            <span class="close-modal" onclick="closeBookModal()">&times;</span>
            <div class="book-cover">
                <h2 id="modalTitle" style="font-size: 2.5em; color: white; text-align: left;"></h2>
                <p id="modalAuthor" class="mb-2" style="font-size: 1.5em; color: white; text-align: left;"></p>
                <div id="modalStatus" class="book-status"></div>
            </div>
            <div class="book-details">
                <div class="book-detail-item">
                    <i class="fas fa-info-circle"></i>
                    <strong>Book ID:</strong>
                    <span id="modalBookId"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-layer-group"></i>
                    <strong>Subject:</strong>
                    <span id="modalCategory"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-building"></i>
                    <strong>Publisher:</strong>
                    <span id="modalPublisher"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-calendar"></i>
                    <strong>Copyright:</strong>
                    <span id="modalPublishedDate"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-book"></i>
                    <strong>Available Copies:</strong>
                    <span id="modalQuantity"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-bookmark"></i>
                    <strong>Edition:</strong>
                    <span id="modalEdition"></span>
                </div>
                <div class="book-detail-item">
                    <i class="fas fa-quote-right"></i>
                    <strong>APA Citation:</strong>
                    <span id="modalAPA"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        function showBookModal(bookData) {
            document.getElementById('modalTitle').textContent = bookData.title;
            document.getElementById('modalAuthor').textContent = 'by ' + bookData.authorName;
            document.getElementById('modalBookId').textContent = bookData.bookId;
            document.getElementById('modalCategory').textContent = bookData.categoryName;
            document.getElementById('modalPublisher').textContent = bookData.publisher;
            document.getElementById('modalPublishedDate').textContent = bookData.publishedYear;
            document.getElementById('modalQuantity').textContent = bookData.quantity;
            document.getElementById('modalEdition').textContent = bookData.edition || 'N/A';
            
            // Set APA citation
            let apaCitation = '';
            if (bookData.authorName === 'No Author') {
                apaCitation = `<em>${bookData.title}</em>. (${bookData.publishedYear}). ${bookData.publisher}`;
            } else {
                apaCitation = `${bookData.authorName} (${bookData.publishedYear}). <em>${bookData.title}</em>. ${bookData.publisher}`;
            }
            document.getElementById('modalAPA').innerHTML = apaCitation;

            // Set status
            const statusElement = document.getElementById('modalStatus');
            if (bookData.quantity > 0) {
                statusElement.textContent = 'Available';
                statusElement.className = 'book-status status-available';
            } else {
                statusElement.textContent = 'Not Available';
                statusElement.className = 'book-status status-borrowed';
            }

            document.getElementById('bookModal').style.display = 'block';
        }

        function closeBookModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
?>
