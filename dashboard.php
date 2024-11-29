<?php
// Include your existing database connection file
include('process/db-connect.php'); // Adjust the path to your actual connection file

// Fetch available column data from tblbooks
// Initialize variables
$totalQuantity = 0;
$totalNotReturned = 0;

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

$total = $totalNotReturned + $totalQuantity;

// Close the database connection (optional if the connection is persistent)
$conn->close();

// Start output buffering to inject this content into the main template
ob_start();
?>

<main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container-fluid">
        <div class="row justify-content-sm-center text-center py-10">
            <div class="col-sm-7 col-md-5">
                <h1>Books Availability Overview</h1>
                <p>Total Available Books : <?php echo $total   ?></p>
                <canvas id="availabilityChart"></canvas> <!-- The bar chart will be rendered here -->
            </div>
        </div>
        <!-- End Row -->
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
</script>

<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
