<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
<a class="sidebar-brand d-flex bg-white align-items-center justify-content-center" href="dashboard.php">
    <!-- Replace this with your logo image -->
    <img src="assets/img/ustp.png" alt="Library Logo" style="height: 65px;">
</a>


    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">DPM 001</div>

    <li class="nav-item">
        <?php if ($_SESSION['accountType'] == 'Admin') { ?>
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseZero" aria-expanded="true" aria-controls="collapseZero">
                <i class="fas fa-user-cog"></i>
                <span>Administration</span>
            </a>
            <div id="collapseZero" class="collapse" aria-labelledby="headingZero" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="list-user.php"><i class="fas fa-users-cog"></i>Librarian Accounts</a>
                </div>
            </div>
        <?php } ?>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-list"></i>
            <span>Borrowers</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-borrower.php?borrowerType=Student"><i class="fas fa-user-graduate"></i> Students</a>
                <a class="collapse-item" href="list-borrower.php?borrowerType=Faculty"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                <a class="collapse-item" href="list-borrower.php?borrowerType=Staff"><i class="fas fa-user-tie"></i> Staff</a>
                <a class="collapse-item" href="list-course.php"><i class="fas fa-book"></i> Course</a>
            </div>
        </div>

        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
            <i class="fas fa-id-card"></i>
            <span> Library Cards</span>
        </a>
        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-receipt.php?receipt=Yes"><i class="fas fa-receipt"></i> Receipt Log</a>
                <a class="collapse-item" href="list-receipt.php?receipt=No"><i class="fas fa-times-circle"></i> Non-Receipt Log</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">DPM 002</div>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
            <i class="fas fa-handshake"></i>
            <span>Reference Assistance Slip</span>
        </a>
        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-reference.php"><i class="fas fa-list-alt"></i> Ref. Assistance List</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">DPM 003</div>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-exchange-alt"></i>
            <span>Borrowed/Returned</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="book-card.php"><i class="fas fa-bookmark"></i> Book Card</a>
                <a class="collapse-item" href="list-penalties.php"><i class="fas fa-money-bill-wave"></i> Penalties</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">DPM 009</div>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
            <i class="fas fa-book-open"></i>
            <span>Book Inventory</span>
        </a>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="list-books.php"><i class="fas fa-book"></i> Books</a>
                <a class="collapse-item" href="list-author.php"><i class="fas fa-user-edit"></i> Author</a>
                <a class="collapse-item" href="list-category.php"><i class="fas fa-tags"></i> Category</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

   
    
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
