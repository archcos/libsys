<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sign-in.php');
    exit;
}

// Start output buffering
ob_start();

// Get filter values
$filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : 'daily';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$selected_book = isset($_POST['book_id']) ? $_POST['book_id'] : '';
$selected_borrower = isset($_POST['borrower_id']) ? $_POST['borrower_id'] : '';
$selected_borrower_id = isset($_POST['borrower_idnum']) ? $_POST['borrower_idnum'] : '';

include('process/db-connect.php');

// Get list of years for the dropdown
$start_year = 2010;
$end_year = 2050;
$years = range($end_year, $start_year);

// Get list of books
$books_query = "SELECT bookId, title FROM tblbooks ORDER BY title";
$books_result = mysqli_query($conn, $books_query);

// Get list of borrowers
$borrowers_query = "SELECT DISTINCT b.idNumber, CONCAT(b.firstName, ' ', b.middleName, ' ', b.surName) as full_name 
                    FROM tblborrowers b 
                    INNER JOIN tblreturnborrow rb ON b.idNumber = rb.borrowerId 
                    ORDER BY full_name";
$borrowers_result = mysqli_query($conn, $borrowers_query);

// Format the input types and values based on filter type
$input_type = 'date';
if ($filter_type == 'monthly') {
    $input_type = 'month';
    if ($start_date) {
        $start_date = date('Y-m', strtotime($start_date));
    }
    if ($end_date) {
        $end_date = date('Y-m', strtotime($end_date));
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Ledger</h1>
        <div>
            <button class="btn btn-info mr-2" data-toggle="modal" data-target="#dateFilterModal">
                <i class="fas fa-calendar"></i> Date Filter
            </button>
            <button class="btn btn-info mr-2" data-toggle="modal" data-target="#additionalFiltersModal">
                <i class="fas fa-filter"></i> Additional Filters
            </button>
            <button class="btn btn-primary" onclick="printTable()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <!-- Print Header (Only visible when printing) -->
                <div class="print-header">
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <img src="assets/img/ustp.png" alt="Library Logo" style="height: 100px;">
                    </div>
                    <div class="text-center mb-4">
                        <h3>University of Science and Technology of Southern Philippines</h3>
                        <h4>Balubal Library System</h4>
                        <h5>Inventory Ledger Report</h5>
                        <?php
                        // Format the filter information
                        $filter_info = array();
                        
                        if ($start_date && $end_date) {
                            switch($filter_type) {
                                case 'daily':
                                    $filter_info[] = "Date: " . date('F d, Y', strtotime($start_date)) . 
                                                   " to " . date('F d, Y', strtotime($end_date));
                                    break;
                                case 'monthly':
                                    $filter_info[] = "Month: " . date('F Y', strtotime($start_date)) . 
                                                   " to " . date('F Y', strtotime($end_date));
                                    break;
                                case 'yearly':
                                    $filter_info[] = "Year: " . $start_date . " to " . $end_date;
                                    break;
                            }
                        }

                        if ($selected_book) {
                            $book_query = "SELECT title FROM tblbooks WHERE bookId = '$selected_book'";
                            $book_result = mysqli_query($conn, $book_query);
                            if ($book_result && $book = mysqli_fetch_assoc($book_result)) {
                                $filter_info[] = "Book: " . $book['title'];
                            }
                        }

                        if ($selected_borrower || $selected_borrower_id) {
                            $borrower_id = $selected_borrower ?: $selected_borrower_id;
                            $borrower_query = "SELECT CONCAT(firstName, ' ', middleName, ' ', surName) as full_name 
                                             FROM tblborrowers WHERE idNumber = '$borrower_id'";
                            $borrower_result = mysqli_query($conn, $borrower_query);
                            if ($borrower_result && $borrower = mysqli_fetch_assoc($borrower_result)) {
                                $filter_info[] = "Borrower: " . $borrower['full_name'] . " (ID: $borrower_id)";
                            }
                        }

                        if (!empty($filter_info)) {
                            echo "<div class='filter-info mt-3'>";
                            echo "<strong>As of:</strong><br>";
                            echo implode("<br>", $filter_info);
                            echo "</div>";
                        }

                        // Add date generated
                        echo "<div class='date-generated mt-3'>";
                        echo "<strong>Date Generated:</strong> " . date('F d, Y h:i A');
                        echo "</div>";
                        ?>
                    </div>
                </div>

                <table class="table table-bordered" id="ledgerTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>ID NUMBER</th>
                            <th>NAME</th>
                            <th>DETAILS</th>
                            <th>BORROW QTY.</th>
                            <th>RETURN QTY.</th>
                            <th>BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Build where clause conditions
                        $conditions = array();
                        
                        if ($start_date && $end_date) {
                            switch($filter_type) {
                                case 'daily':
                                    $conditions[] = "(transaction_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59')";
                                    break;
                                case 'monthly':
                                    $start_month = date('Y-m-01', strtotime($start_date));
                                    $end_month = date('Y-m-t', strtotime($end_date));
                                    $conditions[] = "(transaction_date BETWEEN '$start_month 00:00:00' AND '$end_month 23:59:59')";
                                    break;
                                case 'yearly':
                                    $start_year = $start_date . '-01-01';
                                    $end_year = $end_date . '-12-31';
                                    $conditions[] = "(transaction_date BETWEEN '$start_year 00:00:00' AND '$end_year 23:59:59')";
                                    break;
                            }
                        }

                        if ($selected_book) {
                            $conditions[] = "(details = (SELECT title FROM tblbooks WHERE bookId = '$selected_book'))";
                        }

                        if ($selected_borrower) {
                            $conditions[] = "(idNumber = '$selected_borrower')";
                        }

                        if ($selected_borrower_id) {
                            $conditions[] = "(idNumber = '$selected_borrower_id')";
                        }

                        $where_clause = "";
                        if (!empty($conditions)) {
                            $where_clause = "HAVING " . implode(" AND ", $conditions);
                        }

                        // Fetch ledger data from database
                        $query = "
                            WITH LedgerData AS (
                                SELECT 
                                    bk.dateAdded as transaction_date,
                                    NULL as idNumber,
                                    'Initial Stock' as borrower_name,
                                    bk.title as details,
                                    0 as borrow_qty,
                                    0 as return_qty,
                                    bk.quantity + IFNULL((
                                        SELECT COUNT(*) 
                                        FROM tblreturnborrow 
                                        WHERE bookId = bk.bookId 
                                        AND returned = 'No'
                                    ), 0) as balance
                                FROM tblbooks bk
                                UNION ALL
                                SELECT 
                                    rb.borrowedDate as transaction_date,
                                    b.idNumber,
                                    CONCAT(b.firstName, ' ', b.middleName, ' ', b.surName) as borrower_name,
                                    bk.title as details,
                                    1 as borrow_qty,
                                    0 as return_qty,
                                    bk.quantity as balance
                                FROM tblreturnborrow rb
                                JOIN tblborrowers b ON rb.borrowerId = b.idNumber
                                JOIN tblbooks bk ON rb.bookId = bk.bookId
                                WHERE rb.borrowedDate IS NOT NULL
                                UNION ALL
                                SELECT 
                                    rb.returnDate as transaction_date,
                                    b.idNumber,
                                    CONCAT(b.firstName, ' ', b.middleName, ' ', b.surName) as borrower_name,
                                    bk.title as details,
                                    0 as borrow_qty,
                                    1 as return_qty,
                                    bk.quantity as balance
                                FROM tblreturnborrow rb
                                JOIN tblborrowers b ON rb.borrowerId = b.idNumber
                                JOIN tblbooks bk ON rb.bookId = bk.bookId
                                WHERE rb.returned = 'Yes' 
                                AND rb.returnDate IS NOT NULL
                            )
                            SELECT * FROM LedgerData
                            $where_clause
                            ORDER BY transaction_date ASC";

                        $result = mysqli_query($conn, $query);
                        
                        if (!$result) {
                            echo "Error in query: " . mysqli_error($conn);
                            exit;
                        }

                        $balances = array();
                        $book_quantities = array(); // Store the original book quantities
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            $book_title = $row['details'];
                            
                            // Initialize balance and book quantity if not yet set
                            if (!isset($balances[$book_title])) {
                                $balances[$book_title] = $row['balance'];
                                // Store the original book quantity
                                $book_quantities[$book_title] = $row['balance'];
                            } else {
                                // Calculate new balance but don't exceed original quantity
                                $new_balance = $balances[$book_title] - $row['borrow_qty'] + $row['return_qty'];
                                $balances[$book_title] = max(0, min($new_balance, $book_quantities[$book_title]));
                            }
                            
                            echo "<tr>";
                            echo "<td>" . ($row['transaction_date'] ? date('Y-m-d', strtotime($row['transaction_date'])) : '-') . "</td>";
                            echo "<td>" . ($row['idNumber'] ?: '-') . "</td>";
                            echo "<td>" . $row['borrower_name'] . "</td>";
                            echo "<td>" . $row['details'] . "</td>";
                            echo "<td>" . $row['borrow_qty'] . "</td>";
                            echo "<td>" . $row['return_qty'] . "</td>";
                            echo "<td>" . $balances[$book_title] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Date Filter Modal -->
<div class="modal fade" id="dateFilterModal" tabindex="-1" role="dialog" aria-labelledby="dateFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateFilterModalLabel">Date Range Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="filterForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Filter Type</label>
                        <select class="form-control" name="filter_type" id="filter_type" onchange="updateDateInputs()">
                            <option value="daily" <?php echo $filter_type == 'daily' ? 'selected' : ''; ?>>Daily</option>
                            <option value="monthly" <?php echo $filter_type == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                            <option value="yearly" <?php echo $filter_type == 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                        </select>
                    </div>
                    <div id="start_date_container">
                        <div class="form-group">
                            <label>Start Date</label>
                            <?php if($filter_type == 'yearly'): ?>
                                <select class="form-control" name="start_date" id="start_date">
                                    <option value="">Select Year</option>
                                    <?php foreach($years as $year): ?>
                                        <option value="<?php echo $year; ?>" <?php echo $start_date == $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="<?php echo $input_type; ?>" class="form-control" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="end_date_container">
                        <div class="form-group">
                            <label>End Date</label>
                            <?php if($filter_type == 'yearly'): ?>
                                <select class="form-control" name="end_date" id="end_date">
                                    <option value="">Select Year</option>
                                    <?php foreach($years as $year): ?>
                                        <option value="<?php echo $year; ?>" <?php echo $end_date == $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="<?php echo $input_type; ?>" class="form-control" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Additional Filters Modal -->
<div class="modal fade" id="additionalFiltersModal" tabindex="-1" role="dialog" aria-labelledby="additionalFiltersModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="additionalFiltersModalLabel">Additional Filters</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="additionalFilters">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Book</label>
                        <select class="form-control" name="book_id" id="book_id">
                            <option value="">All Books</option>
                            <?php while($book = mysqli_fetch_assoc($books_result)): ?>
                                <option value="<?php echo $book['bookId']; ?>" <?php echo $selected_book == $book['bookId'] ? 'selected' : ''; ?>>
                                    <?php echo $book['title']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Borrower Name</label>
                        <select class="form-control" name="borrower_id" id="borrower_id" onchange="updateBorrowerId(this.value)">
                            <option value="">All Borrowers</option>
                            <?php 
                            mysqli_data_seek($borrowers_result, 0);
                            while($borrower = mysqli_fetch_assoc($borrowers_result)): 
                            ?>
                                <option value="<?php echo $borrower['idNumber']; ?>" 
                                        data-id="<?php echo $borrower['idNumber']; ?>"
                                        <?php echo $selected_borrower == $borrower['idNumber'] ? 'selected' : ''; ?>>
                                    <?php echo $borrower['full_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Borrower ID</label>
                        <input type="text" class="form-control" name="borrower_idnum" id="borrower_idnum" 
                               value="<?php echo $selected_borrower_id; ?>" 
                               onchange="updateBorrowerName(this.value)"
                               placeholder="Enter ID Number">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything except the table and print header */
    body * {
        visibility: hidden;
    }
    .print-header, .print-header *,
    #ledgerTable, #ledgerTable * {
        visibility: visible;
    }
    #ledgerTable {
        position: relative;
        left: 0;
        top: 0;
    }
    
    /* Print header styling */
    .print-header {
        margin-bottom: 20px;
    }
    .print-header h3,
    .print-header h4,
    .print-header h5 {
        margin: 5px 0;
    }
    .filter-info {
        font-size: 14px;
        margin: 10px 0;
    }
    .date-generated {
        font-size: 12px;
        margin-top: 10px;
    }
    
    /* Remove DataTables elements when printing */
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }

    /* Ensure table fits on page */
    table {
        font-size: 12px;
    }
    
    /* Add page break rules */
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    thead {
        display: table-header-group;
    }
}

/* Hide print header during normal view */
.print-header {
    display: none;
}
@media print {
    .print-header {
        display: block;
    }
}
</style>

<script>
$(document).ready(function() {
    $('#ledgerTable').DataTable({
        "order": [[0, "desc"]]
    });

    // Initialize Select2 for better dropdown experience
    $('#book_id, #borrower_id').select2({
        placeholder: "Select an option",
        allowClear: true,
        dropdownParent: $('#additionalFiltersModal')
    });

    // Set initial borrower ID if a borrower is selected
    var selectedBorrower = $('#borrower_id').val();
    if (selectedBorrower) {
        updateBorrowerId(selectedBorrower);
    }

    // Set initial borrower name if ID is entered
    var selectedBorrowerId = $('#borrower_idnum').val();
    if (selectedBorrowerId) {
        updateBorrowerName(selectedBorrowerId);
    }

    // Clear both fields when modal is closed if no selection
    $('#additionalFiltersModal').on('hidden.bs.modal', function () {
        if (!$('#borrower_id').val() && !$('#borrower_idnum').val()) {
            $('#borrower_id').val('').trigger('change');
            $('#borrower_idnum').val('');
        }
    });
});

function updateDateInputs() {
    const filterType = $('#filter_type').val();
    const startContainer = $('#start_date_container');
    const endContainer = $('#end_date_container');
    
    // Clear the values
    startContainer.empty();
    endContainer.empty();
    
    if (filterType === 'yearly') {
        // Add year dropdowns
        const yearSelect = `
            <div class="form-group">
                <label>Start Year</label>
                <select class="form-control" name="start_date" id="start_date">
                    <option value="">Select Year</option>
                    <?php foreach($years as $year): ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>`;
        const endYearSelect = yearSelect.replace('Start Year', 'End Year').replace('start_date', 'end_date');
        
        startContainer.html(yearSelect);
        endContainer.html(endYearSelect);
    } else {
        // Add date/month inputs
        const inputType = filterType === 'monthly' ? 'month' : 'date';
        const startInput = `
            <div class="form-group">
                <label>Start Date</label>
                <input type="${inputType}" class="form-control" name="start_date" id="start_date">
            </div>`;
        const endInput = startInput.replace('Start Date', 'End Date').replace('start_date', 'end_date');
        
        startContainer.html(startInput);
        endContainer.html(endInput);
    }
}

function printTable() {
    window.print();
}

function updateBorrowerId(selectedValue) {
    document.getElementById('borrower_idnum').value = selectedValue;
}

function updateBorrowerName(idNumber) {
    var select = document.getElementById('borrower_id');
    var options = select.options;
    
    // Reset selection first
    select.value = '';
    
    // Find and select the matching option
    for(var i = 0; i < options.length; i++) {
        if(options[i].value === idNumber) {
            select.value = idNumber;
            // Trigger Select2 update
            $('#borrower_id').trigger('change');
            break;
        }
    }
}
</script>

<?php
$content = ob_get_clean();
include('templates/main.php');
?> 