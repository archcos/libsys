<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon">
            <img src="assets/img/ustp.png" alt="Logo" width="38" height="38">
        </div>
        <div class="sidebar-brand-text mx-3">USTP Balubal</div>
    </a>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-house-user"></i> <!-- Changed to 'fa-house-user' -->
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">



    <!-- Nav Item - My Transactions Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-exchange-alt"></i> <!-- Changed to 'fa-exchange-alt' -->
            <span>My Transactions</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-transactions.php"><i class="fas fa-clipboard-list"></i> Transactions</a> <!-- Changed to 'fa-clipboard-list' -->
                <a class="collapse-item" href="list-books.php?returned=No"><i class="fas fa-book-open"></i> Borrowed Books</a> <!-- Changed to 'fa-book-open' -->
                <a class="collapse-item" href="list-books.php?returned=Yes"><i class="fas fa-archive"></i> Returned Books</a> <!-- Changed to 'fa-archive' -->
                <a class="collapse-item" href="list-penalties.php"><i class="fas fa-archive"></i> Penalties</a> <!-- Changed to 'fa-archive' -->
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Logout Button -->
    <li class="nav-item mt-auto">
        <form action="process/logout.php" method="POST" class="p-0 text-center">
            <button type="submit" class="btn btn-block text-white">
                <i class="fas fa-sign-out-alt"></i> <!-- Kept 'fa-sign-out-alt' icon -->
                Logout
            </button>
        </form>
    </li>
</ul>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript -->
<script>
    $(document).ready(function () {
        $('#checkidNumber').on('click', function () {
            const idNumber = $('#idNumber').val().trim();
            if (idNumber) {
                $.ajax({
                    url: 'process/edit-check.php',
                    type: 'POST',
                    data: { idNumber: idNumber },
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.exists) {
                            window.location.href = `edit-borrower.php?idNumber=${idNumber}`;
                        } else {
                            alert('Borrower ID does not exist. Please try again.');
                        }
                    },
                    error: function () {
                        alert('An error occurred while checking the Borrower ID.');
                    }
                });
            } else {
                alert('Please enter a Borrower ID.');
            }
        });

        $('#borrowerModal').on('hidden.bs.modal', function () {
            $('#idNumber').val('');
        });
    });
</script>
