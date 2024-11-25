<?php
// Include your existing database connection file
include('db-connect.php'); // Make sure to adjust the path to your actual connection file

// Fetch sales data from the database
$sql = "SELECT month, sales FROM sales_data";
$result = $conn->query($sql);

// Arrays to hold data for the chart
$months = [];
$sales = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $months[] = $row['month'];
        $sales[] = $row['sales'];
}
} else {
    echo "No data found";
}

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
                <img class="img-fluid mb-5" src="assets/svg/illustrations/graphs.svg" alt="Image Description" style="max-width: 21rem;">

                <h1>Sales Overview</h1>
                <p>Track the sales performance by month.</p>
                <canvas id="salesChart"></canvas> <!-- The bar chart will be rendered here -->

                <a class="btn btn-primary" href="index.html">Create my first campaign</a>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Content -->
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>, // Labels from database
            datasets: [{
                label: 'Sales',
                data: <?php echo json_encode($sales); ?>, // Sales data from database
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
