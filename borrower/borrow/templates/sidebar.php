<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon">
                    <img src="assets/img/logo.png" alt="Logo" width="38" height="38">
                </div>
                <div class="sidebar-brand-text mx-3">USTP Balubal</div>
            </a>
    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        DPM 001
    </div>


    <!-- Nav Item - Borrower Data Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-cog"></i>
            <span>My Transactions</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-transactions.php">Transactions</a>
                <a class="collapse-item" href="list-books.php?returned=No">Borrowed Books</a>
                <a class="collapse-item" href="list-books.php?returned=Yes">Returned Books</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

        <!-- Divider -->

        <!-- Logout Button -->
        <li class="nav-item mt-auto">
            <form action="process/logout.php" method="POST" class="p-0 text-center">
                <button type="submit" class="btn btn-block text-white">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-white"></i>
                    Logout
                </button>
            </form>
        </li>


</ul>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript -->
<script>
    // document.getElementById('proceedButton').addEventListener('click', function () {
    //     const borrowerType = document.querySelector('input[name="borrowerType"]:checked');
    //     if (borrowerType) {
    //         const selectedType = borrowerType.value;
    //         window.location.href = `add-borrower.php?borrowerType=${selectedType}`;
    //     } else {
    //         alert('Please select a borrower type before proceeding.');
    //     }
    // });

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
