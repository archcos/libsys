<?php

include('process/db-connect.php'); 

ob_start();

if (isset($_GET['idNumber'])) {
    $idNumber = $_GET['idNumber'];

    $sql = "SELECT idNumber, borrowerType, libraryId, surName, firstName, middleName, course, year, position, gender, birthDate, homeAddress, remarks FROM tblborrowers WHERE idNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $idNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $borrower = $result->fetch_assoc();
    } else {
        echo "idNumber not found.";
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "idNumber is required.";
    exit();
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status == 'success') {
        $message = 'Borrower information updated successfully!';
        $alertClass = 'alert-success';
    } elseif ($status == 'error') {
        $message = 'There was an error updating the borrower information. Please try again.';
        $alertClass = 'alert-danger';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Borrower</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        button {
                display: block;
                width: 200px; /* or any specific width */
                margin: 0 auto; /* This centers the button horizontally */
                padding: 10px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                cursor: pointer;
            }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1>Edit Borrower Information</h1>

     <!-- Display success or error message -->
     <?php if (isset($message)): ?>
        <div class="alert <?php echo $alertClass; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>


    <form action="process/editing-borrower.php" method="POST">
        <input type="hidden" name="idNumber" value="<?php echo $borrower['idNumber']; ?>">

        <div class="form-group">
            <label for="surName">Surname</label>
            <input type="text" class="form-control" id="surName" name="surName" value="<?php echo $borrower['surName']; ?>" required>
        </div>

        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $borrower['firstName']; ?>" required>
        </div>

        <div class="form-group">
            <label for="middleName">Middle Name</label>
            <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo $borrower['middleName']; ?>">
        </div>

        <div class="form-group">
            <label for="borrowerType">Borrower Type</label>
            <select class="form-control" id="borrowerType" name="borrowerType" required>
                <option value="Student" <?php echo ($borrower['borrowerType'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                <option value="Faculty" <?php echo ($borrower['borrowerType'] == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                <option value="Staff" <?php echo ($borrower['borrowerType'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
            </select>
        </div>

        <div class="form-group">
            <label for="libraryId">Library ID</label>
            <input type="number" class="form-control" id="libraryId" name="libraryId" value="<?php echo $borrower['libraryId']; ?>" required>
        </div>

        <div class="form-group" id="courseField">
            <label for="course">Course</label>
            <input type="text" class="form-control" id="course" name="course" value="<?php echo $borrower['course']; ?>" required>
        </div>

        <div class="form-group" id="yearField">
            <label for="year">Year</label>
            <input type="number" class="form-control" id="year" name="year" value="<?php echo $borrower['year']; ?>" required>
        </div>

        <div class="form-group" id="positionField">
            <label for="position">Position</label>
            <input type="text" class="form-control" id="position" name="position" value="<?php echo $borrower['position']; ?>">
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male" <?php echo ($borrower['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($borrower['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="birthDate">Birth Date</label>
            <input type="date" class="form-control" id="birthDate" name="birthDate" value="<?php echo $borrower['birthDate']; ?>" required>
        </div>

        <div class="form-group">
            <label for="homeAddress">Home Address</label>
            <textarea class="form-control" id="homeAddress" name="homeAddress" rows="3" required><?php echo $borrower['homeAddress']; ?></textarea>
        </div>

        <div class="form-group">
            <label for="remarks">Remarks</label>
            <select class="form-control" id="remarks" name="remarks" required>
                <option value="1" <?php echo ($borrower['remarks'] == 1) ? 'selected' : ''; ?>>Activated</option>
                <option value="0" <?php echo ($borrower['remarks'] == 0) ? 'selected' : ''; ?>>Deactivated</option>
            </select>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<!-- Add Bootstrap JS and jQuery if needed -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

<script>
    // Function to show/hide fields based on the borrowerType
    function toggleFieldsBasedOnType() {
        var borrowerType = document.getElementById('borrowerType').value;
        
        if (borrowerType === 'Student') {
            document.getElementById('positionField').classList.add('hidden');
            document.getElementById('courseField').classList.remove('hidden');
            document.getElementById('yearField').classList.remove('hidden');
        } else if (borrowerType === 'Faculty' || borrowerType === 'Staff') {
            document.getElementById('positionField').classList.remove('hidden');
            document.getElementById('courseField').classList.add('hidden');
            document.getElementById('yearField').classList.add('hidden');
        }
    }

    // On page load, call the function to check the borrowerType
    window.onload = toggleFieldsBasedOnType;

    // When the borrowerType is changed, call the function to toggle the fields
    document.getElementById('borrowerType').addEventListener('change', toggleFieldsBasedOnType);
</script>

</body>
</html>

<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
