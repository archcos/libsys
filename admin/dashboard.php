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
<div class="col-15 mb-3">
  <div class="card shadow-sm border-start border-4 border-primary">
    <div class="card-body fs-5 fw-semibold text-SECONDARY">
    <span class="wave">👋</span> Welcome, <?php echo $_SESSION['firstName'] . ' ' . $_SESSION['lastName']; ?>!
    </div>
  </div>
</div>
<style>



@keyframes wave-animation {
  0% { transform: rotate(0deg); }
  10% { transform: rotate(14deg); }
  20% { transform: rotate(-8deg); }
  30% { transform: rotate(14deg); }
  40% { transform: rotate(-4deg); }
  50% { transform: rotate(10deg); }
  60% { transform: rotate(0deg); }
  100% { transform: rotate(0deg); }
}

.wave {
  display: inline-block;
  transform-origin: 70% 70%;
  animation: wave-animation 2s infinite;
}


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
    .card-left {

        width: 80vh;

    }
    .tiknik {

        padding-left: 50px;
       

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

<!-- <main id="content" role="main" class="main"> -->
    <!-- Content -->


                
                

    <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Borrowers</div>
                                                <p><?php echo $totalBorrowers; ?></p>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Books</div>
                                            <p><?php echo $totalQuantity; ?></p>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Available Books -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Available Books
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                <p><?php echo $totalQuantity - $totalNotReturned; ?></p>
                                                </div>
                                                <div class="col">
                                                    
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Borrowed Books -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Borrowed Books</div>
                                            <p><?php echo $totalNotReturned; ?></p>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hand-holding fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
  <!-- Left Column -->
  <div class="col-md-4">
    <!-- Borrower Types Distribution -->
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-primary text-white fw-bold">
        <span>Borrower Distribution</span>
      </div>
      <div class="card-body">
        <div style="height: 300px;">
          <canvas id="borrowerTypesChart"></canvas>
        </div>
      </div>
    </div>
  </div>            

     <!--                
    <div class="card-body">
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
    </div>
</div> -->    <div class="tiknik">

                <p>
                <?php
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$years = range(2010, 2040); // Change this range as you like
?>
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
     
        <div class="card-left shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Borrowed Books per Month for <?php echo $selectedYear; ?></h6>
    </div>

    <div class="card-body">
        <canvas id="monthlyBorrowedChart"></canvas>
    </div>

    
    <!-- <div class="card-right">
      
        <div> -->
</div>
            </div>
        </div>
       
        </div>
    </div>


    
    <!-- End Content -->
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   
   var ctx = document.getElementById('monthlyBorrowedChart').getContext('2d');
var monthlyBorrowedChart = new Chart(ctx, {
    type: 'bar',  // Type of chart (Bar chart in this case)
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
                beginAtZero: false,  // Prevents starting from 0 on the Y-axis
                min: 0,  // Start from 0 on the Y-axis
                ticks: {
                    stepSize: 1  // Ensures tick marks are placed at regular intervals of 1
                }
            }
        }
    }
});

</script>
<!-- JS for Notes and Checklist -->
<script>
  // Notes
  function saveNote() {
    const note = document.getElementById("quickNotes").value;
    localStorage.setItem("quickNote", note);
    alert("Note saved!");
  }
  document.getElementById("quickNotes").value = localStorage.getItem("quickNote") || "";

  // Checklist
  const checklist = document.getElementById("checklist");
  const savedTasks = JSON.parse(localStorage.getItem("tasks")) || [];

  function renderTasks() {
    checklist.innerHTML = "";
    savedTasks.forEach((task, index) => {
      const li = document.createElement("li");
      li.className = "list-group-item d-flex justify-content-between align-items-center";
      li.innerHTML = `<span>${task}</span> <button class="btn btn-sm btn-danger" onclick="deleteTask(${index})">Remove</button>`;
      checklist.appendChild(li);
    });
  }

  function addTask() {
    const taskInput = document.getElementById("taskInput");
    if (taskInput.value.trim() !== "") {
      savedTasks.push(taskInput.value.trim());
      localStorage.setItem("tasks", JSON.stringify(savedTasks));
      taskInput.value = "";
      renderTasks();
    }
  }

  function deleteTask(index) {
    savedTasks.splice(index, 1);
    localStorage.setItem("tasks", JSON.stringify(savedTasks));
    renderTasks();
  }

  renderTasks();
</script>
<script>
// Add Borrower Types Pie Chart
async function initializeBorrowerTypesChart() {
    try {
        const response = await fetch('process/get-borrower-types.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        
        const pieCtx = document.getElementById('borrowerTypesChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',   // Blue for Students
                        'rgba(255, 206, 86, 0.8)',   // Yellow for Faculty
                        'rgba(75, 192, 192, 0.8)',   // Green for Staff
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error fetching borrower types data:', error);
    }
}

// Initialize the pie chart when the page loads
window.addEventListener('load', initializeBorrowerTypesChart);
</script>
<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
