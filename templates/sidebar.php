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
                    <a class="collapse-item" href="#" data-toggle="modal" data-target="#borrowerModal">Edit Borrower Data</a>
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
                        <!-- Borrower ID Input -->
                        <div class="form-group">
                            <label for="borrowerId">Borrower ID</label>
                            <input type="text" class="form-control" id="borrowerId" placeholder="Enter Borrower ID">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="checkBorrowerId">Check Borrower ID</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

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
    $(document).ready(function() {
        // When the 'Check Borrower ID' button is clicked
        $('#checkBorrowerId').on('click', function() {
            var borrowerId = $('#borrowerId').val().trim(); // Get the Borrower ID

            if (borrowerId !== '') {
                // AJAX request to check if Borrower ID exists
                $.ajax({
                    url: 'edit-check.php', // The PHP file that checks the database
                    type: 'POST',
                    data: { borrowerId: borrowerId }, // Send the Borrower ID to the PHP file
                    success: function(response) {
                        var data = JSON.parse(response); // Parse the JSON response

                        if (data.exists) {
                            // If Borrower ID exists, redirect to the edit page for that borrower
                            window.location.href = 'edit-borrower.php?borrowerId=' + borrowerId;
                        } else {
                            // If Borrower ID does not exist, reload the page and show a notification
                            alert('Borrower ID does not exist. Please try again.');
                            // location.reload(); // Reload the page
                        }
                    },
                    error: function() {
                        alert('An error occurred while checking the Borrower ID.');
                    }
                });
            } else {
                // If no Borrower ID entered, show an alert
                alert('Please enter a Borrower ID.');
            }
        });

        // Ensure the modal resets when it's closed
        $('#borrowerModal').on('hidden.bs.modal', function() {
            // Reset form fields
            $('#borrowerId').val('');  // Clear the input field
        });
    });

</script>