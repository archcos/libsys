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
        Interface
    </div>

    <!-- Nav Item - Borrower Data Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <i class="fas fa-fw fa-cog"></i>
            <span>Borrower Data</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#" data-toggle="modal" data-target="#addBorrowerModal">Add New Borrower</a>
                <a class="collapse-item" href="#" data-toggle="modal" data-target="#borrowerModal">Edit Borrower Data</a>
                <a class="collapse-item" href="list-borrower.php">Borrowers List</a>
            </div>
        </div>
    </li>

    <!-- Modal - Add Borrower -->
    <div class="modal fade" id="addBorrowerModal" tabindex="-1" role="dialog" aria-labelledby="addBorrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBorrowerModalLabel">Select Borrower Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Please select the type of borrower:</p>
                    <div class="btn-group btn-group-toggle d-flex justify-content-center" data-toggle="buttons">
                        <label class="btn btn-outline-primary mx-2">
                            <input type="radio" name="borrowerType" value="Student" id="studentOption"> Student
                        </label>
                        <label class="btn btn-outline-primary mx-2">
                            <input type="radio" name="borrowerType" value="Faculty" id="facultyOption"> Faculty
                        </label>
                        <label class="btn btn-outline-primary mx-2">
                            <input type="radio" name="borrowerType" value="Staff" id="staffOption"> Staff
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="proceedButton">Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - Edit Borrower -->
    <div class="modal fade" id="borrowerModal" tabindex="-1" role="dialog" aria-labelledby="borrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="borrowerModalLabel">Enter Borrower ID</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="idNumber">Borrower ID</label>
                        <input type="text" class="form-control" id="idNumber" placeholder="Enter Borrower ID">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="checkidNumber">Check Borrower ID</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nav Item - Modules Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Transactions</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="add-book.php">Add Book</a>
                <a class="collapse-item" href="borrow-book.php">Borrow Book</a>
                <a class="collapse-item" href="due-date.php">Book Due Dates</a>
            </div>
        </div>
    </li>

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript -->
<script>
    document.getElementById('proceedButton').addEventListener('click', function () {
        const borrowerType = document.querySelector('input[name="borrowerType"]:checked');
        if (borrowerType) {
            const selectedType = borrowerType.value;
            window.location.href = `add-borrower.php?borrowerType=${selectedType}`;
        } else {
            alert('Please select a borrower type before proceeding.');
        }
    });

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
