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

    <li class="nav-item">
    <?php if ($_SESSION['accountType'] == 'Admin') { ?>
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseZero" aria-expanded="true" aria-controls="collapseOne">
                <i class="fas fa-fw fa-cog"></i>
                <span>Administration</span>
            </a>
            <div id="collapseZero" class="collapse" aria-labelledby="headingZero" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="list-user.php">Manage Librarian Accounts</a>
                </div>
            </div>
        <?php } ?>
    </li>

    <!-- Nav Item - Borrower Data Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-cog"></i>
            <span>Master List</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-borrower.php?borrowerType=Student">Students</a>
                <a class="collapse-item" href="list-borrower.php?borrowerType=Faculty">Faculty</a>
                <a class="collapse-item" href="list-borrower.php?borrowerType=Staff">Staff</a>
            </div>
        </div>
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-cog"></i>
            <span>Library Cards</span>
        </a>
        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-receipt.php?receipt=Yes">Receipt Log</a>
                <a class="collapse-item" href="list-receipt.php?receipt=No">Non-Receipt Log</a>
               
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        DPM 002
    </div>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-cog"></i>
            <span>Reference Assistance Slip</span>
        </a>
        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-reference.php">Reference Assistance List</a>
            </div>
        </div>
    </li>


    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        DPM 003
    </div>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-cog"></i>
            <span>Borrowing / Returning</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="borrow-book.php">Borrow/Return Book</a>
                <a class="collapse-item" href="book-card.php">Book Card</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        DPM 009
    </div>

    <!-- Nav Item - Modules Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Book Inventory</span>
        </a>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-books.php">Books</a>
                <a class="collapse-item" href="list-author.php">Author</a>
                <a class="collapse-item" href="list-category.php">Category</a>
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
