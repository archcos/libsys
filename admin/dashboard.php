<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: sign-in.php');  // Change 'login.php' to your login page
    exit;  // Make sure the script stops executing after the redirect
}

// Include your existing database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file

// Fetch list of available years from the database
$sqlYears = "SELECT DISTINCT YEAR(borrowedDate) AS year FROM tblreturnborrow ORDER BY year DESC";
$resultYears = $conn->query($sqlYears);
$years = [];
if ($resultYears && $resultYears->num_rows > 0) {
    while ($row = $resultYears->fetch_assoc()) {
        $years[] = $row['year'];
}
}

// Initialize variables
$totalQuantity = 0;
$totalNotReturned = 0;
$totalBorrowers = 0; // Variable for Total Borrowers

// Fetch total quantity from tblbooks
$sqlQuantity = "SELECT SUM(quantity) AS total_quantity FROM tblbooks";
$resultQuantity = $conn->query($sqlQuantity);
if ($resultQuantity && $resultQuantity->num_rows > 0) {
    $row = $resultQuantity->fetch_assoc();
    $totalQuantity = $row['total_quantity'];
}

// Fetch total records with returned = 'No' from tblreturnborrow
$sqlNotReturned = "SELECT COUNT(*) AS total_not_returned FROM tblreturnborrow WHERE returned = 'No'";
$resultNotReturned = $conn->query($sqlNotReturned);
if ($resultNotReturned && $resultNotReturned->num_rows > 0) {
    $row = $resultNotReturned->fetch_assoc();
    $totalNotReturned = $row['total_not_returned'];
}

// Fetch total number of borrowers from tblborrowers
$sqlBorrowers = "SELECT COUNT(*) AS total_borrowers FROM tblborrowers";
$resultBorrowers = $conn->query($sqlBorrowers);
if ($resultBorrowers && $resultBorrowers->num_rows > 0) {
    $row = $resultBorrowers->fetch_assoc();
    $totalBorrowers = $row['total_borrowers'];
}

$total = $totalNotReturned + $totalQuantity;

// Fetch borrowed books data for the selected year (if any)
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$monthlyData = [];
for ($month = 1; $month <= 12; $month++) {
    $sqlMonthlyBorrowed = "SELECT COUNT(*) AS borrowed_count 
                           FROM tblreturnborrow 
                           WHERE YEAR(borrowedDate) = '$selectedYear' 
                           AND MONTH(borrowedDate) = '$month' 
                           AND returned = 'No'";
    $resultMonthlyBorrowed = $conn->query($sqlMonthlyBorrowed);
    if ($resultMonthlyBorrowed && $resultMonthlyBorrowed->num_rows > 0) {
        $row = $resultMonthlyBorrowed->fetch_assoc();
        $monthlyData[] = $row['borrowed_count'];
    } else {
        $monthlyData[] = 0; // No data for this month
    }
}

// Close the database connection (optional if the connection is persistent)
$conn->close();

// Start output buffering to inject this content into the main template
ob_start();
$status = isset($_GET['status']) ? $_GET['status'] : ''; // Get the status from the URL parameter

?>

<style>
    .remind-btn {
        display: inline-flex;
        align-items: center;
        font-size: 16px;
        background-color: yellow;
        color: black;
        border: 2px;
        border-color: black;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .remind-btn:hover {
        background-color: lime;
    }

    .remind-btn i {
        margin-right: 10px; /* Space between icon and text */
    }

    /* Styling for the rectangular boxes */
    /* Styling for the rectangular boxes */
    .stats-container {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }

    .stat-box {
        width: 20%;
        padding: 10px;
        background-color: #f0f0f0;
        border-radius: 8px;
        text-align: center;
    }

    .stat-box h4 {
        margin: 0;
        font-size: 16px;
        color: #333;
    }

    .stat-box p {
        font-size: 20px;
        margin: 10px 0 0;
        font-weight: bold;
    }

    /* Unique border colors for each box */
    .total-borrowers {
        border: 2px solid #4CAF50; /* Green */
    }

    .total-books {
        border: 2px solid #2196F3; /* Blue */
    }

    .available-books {
        border: 2px solid #FF9800; /* Orange */
    }

    .borrowed-books {
        border: 2px solid #F44336; /* Red */
    }

</style>

<?php if ($status === 'success'): ?>
    <div class="message success" style="color: green; background: #dff0d8; padding: 10px; border: 1px solid #d0e9c6; margin-bottom: 20px;">
        <strong>Success!</strong> Email Sent Successfully!..
    </div>
<?php elseif ($status === 'exists'): ?>
    <div class="message exists" style="color: white; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
        <strong>Error!</strong> Email didn't sent.
    </div>
<?php elseif ($status === 'error'): ?>
    <div class="message fail" style="color: red; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
        <strong>Error!</strong> There was an issue with sending the email. Please try again.
    </div>
<?php elseif ($status === 'noreturn'): ?>
    <div class="message fail" style="color: red; background: #f2dede; padding: 10px; border: 1px solid #ebccd1; margin-bottom: 20px;">
        <strong>Reminder!</strong> No books are due for return in the next 2 days.
    </div>
<?php endif; ?>

<main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container-fluid">
        <div class="row justify-content-sm-center text-center py-10">
            <div class="col-sm-7 col-md-5">
                <p>
                    <form method="POST" action="process/send-email.php">
                        <button type="submit" name="send_notifications" class="remind-btn">
                            <i class="fas fa-bell"></i> Send Email
                        </button>
                    </form>
                </p>

                <h1>Books Availability Overview</h1>
                <!-- Rectangular boxes for stats -->
                <div class="stats-container">
                    <div class="stat-box total-borrowers">
                        <h4>Total Borrowers</h4>
                        <p><?php echo $totalBorrowers; ?></p>
                    </div>
                    <div class="stat-box total-books">
                        <h4>Total Books</h4>
                        <p><?php echo $totalQuantity; ?></p>
                    </div>
                    <div class="stat-box available-books">
                        <h4>Available Books</h4>
                        <p><?php echo $totalQuantity - $totalNotReturned; ?></p>
                    </div>
                    <div class="stat-box borrowed-books">
                        <h4>Borrowed Books</h4>
                        <p><?php echo $totalNotReturned; ?></p>
                    </div>
                </div>

                
                <canvas id="availabilityChart"></canvas> <!-- The bar chart will be rendered here -->
            </div>
        </div>
        <p>
                    <form method="POST" action="">
                        <select name="year" onchange="this.form.submit()">
                            <?php foreach ($years as $year) : ?>
                                <option value="<?php echo $year; ?>" <?php echo $year == $selectedYear ? 'selected' : ''; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </p>

        <!-- Borrowed Books per Month Chart -->
        <h3>Borrowed Books per Month for <?php echo $selectedYear; ?></h3>
        <canvas id="monthlyBorrowedChart"></canvas>

    </div>
    <!-- End Content -->
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('availabilityChart').getContext('2d');
    var availabilityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Available : <?php echo $totalQuantity ?>', 'Borrowed : <?php echo $totalNotReturned ?>'], // Labels for the chart
            datasets: [{
                label: 'Book Count',
                data: [<?php echo $totalQuantity; ?>, <?php echo $totalNotReturned; ?>], // Data from the database
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)', // Color for "Available"
                    'rgba(255, 99, 132, 0.2)'  // Color for "Not Available"
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)', // Border for "Available"
                    'rgba(255, 99, 132, 1)'  // Border for "Not Available"
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    var ctx = document.getElementById('monthlyBorrowedChart').getContext('2d');
    var monthlyBorrowedChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Months
            datasets: [{
                label: 'Borrowed Books',
                data: <?php echo json_encode($monthlyData); ?>, // Data for borrowed books per month
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
