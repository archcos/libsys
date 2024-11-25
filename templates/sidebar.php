<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
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

    <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <i class="fas fa-fw fa-cog"></i>
                <span>Registry</span>
            </a>
            <div id="collapseOne" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="#" data-toggle="modal" data-target="#addBorrowerModal">Add New Borrower</a>
                    <a class="collapse-item" href="registry/indicator.php">Edit Borrower Data</a>
                    <a class="collapse-item" href="registry/location.php">Print Document</a>
                </div>
            </div>
        </li>

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

    
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Modules</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="modules/target.php">Target</a>
                <a class="collapse-item" href="modules/test.php">Test</a>
            </div>
        </div>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>


<script>
    document.getElementById('proceedButton').addEventListener('click', function() {
        // Get the selected borrower type
        const borrowerType = document.querySelector('input[name="borrowerType"]:checked');
        if (borrowerType) {
            const selectedType = borrowerType.value;

            // Redirect or pass the selected value to another file
            window.location.href = `add-borrower.php?borrowerType=${selectedType}`;
        } else {
            alert('Please select a borrower type before proceeding.');
        }
    });
</script>