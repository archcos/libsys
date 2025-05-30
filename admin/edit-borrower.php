<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: sign-in.php');  // Change 'login.php' to your login page
    exit;  // Make sure the script stops executing after the redirect
}


include('process/db-connect.php'); 

ob_start();

if (isset($_GET['idNumber'])) {
    $idNumber = $_GET['idNumber'];

    $sql = "SELECT b.idNumber, b.borrowerType, b.libraryId, b.surName, b.firstName, b.middleName, b.emailAddress, b.course, b.year, b.position, b.gender, b.birthDate, b.homeAddress, b.remarks, c.courseName 
    FROM tblborrowers b
    LEFT JOIN tblcourses c ON b.course = c.courseId
    WHERE idNumber = ?";
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

    // Fetch all courses for the dropdown
    $coursesQuery = "SELECT courseId, courseName, level FROM tblcourses ORDER BY level, courseName";
    $coursesResult = $conn->query($coursesQuery);

    $courses = [];
    while ($row = $coursesResult->fetch_assoc()) {
        $courses[] = $row;
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
    <!-- Material Design for Bootstrap (MDB) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }
        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn {
            border-radius: 4px;
            padding: 8px 16px;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .hidden {
            display: none;
        }
        .form-group label { font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select {
            padding: 8px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Borrower Information</h6>
    </div>
    <div class="card-body">
        <div class="container">
            <?php if (isset($message)): ?>
                <div class="alert <?php echo $alertClass; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="process/editing-borrower.php" method="POST">
                <input type="hidden" name="idNumber" value="<?php echo $borrower['idNumber']; ?>">
                <div class="form-row">
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
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="emailAddress">Email Address</label>
                        <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?php echo $borrower['emailAddress']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="borrowerType">Borrower Type</label>
                        <select class="form-control" id="borrowerType" name="borrowerType" required onchange="toggleFieldsBasedOnType()">
                            <option value="Student" <?php echo ($borrower['borrowerType'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                            <option value="Faculty" <?php echo ($borrower['borrowerType'] == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                            <option value="Staff" <?php echo ($borrower['borrowerType'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="libraryId">Library ID</label>
                        <input type="number" class="form-control" id="libraryId" name="libraryId" value="<?php echo $borrower['libraryId']; ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group" id="courseField">
                        <label for="course">Course</label>
                        <select class="form-control" id="course" name="course">
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                                <optgroup label="<?php echo htmlspecialchars($course['level']); ?>">
                                    <option value="<?php echo $course['courseId']; ?>" <?php echo ($borrower['course'] == $course['courseId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['courseName']); ?>
                                    </option>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" id="yearField">
                        <label for="year">Year</label>
                        <select class="form-control" id="year" name="year">
                            <option value="">Select Year</option>
                            <option value="1" <?php echo ($borrower['year'] == '1') ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo ($borrower['year'] == '2') ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo ($borrower['year'] == '3') ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo ($borrower['year'] == '4') ? 'selected' : ''; ?>>4th Year</option>
                            <option value="5" <?php echo ($borrower['year'] == '5') ? 'selected' : ''; ?>>5th Year</option>
                        </select>
                    </div>
                    <div class="form-group" id="positionField">
                        <label for="position">Position</label>
                        <input type="text" class="form-control" id="position" name="position" value="<?php echo $borrower['position']; ?>">
                    </div>
                </div>
                <div class="form-row">
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
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <select class="form-control" id="remarks" name="remarks" required>
                            <option value="1" <?php echo ($borrower['remarks'] == 1) ? 'selected' : ''; ?>>Activated</option>
                            <option value="0" <?php echo ($borrower['remarks'] == 0) ? 'selected' : ''; ?>>Deactivated</option>
                        </select>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    function toggleFieldsBasedOnType() {
        const borrowerType = document.getElementById('borrowerType').value;
        const courseField = document.getElementById('courseField');
        const yearField = document.getElementById('yearField');
        const positionField = document.getElementById('positionField');

        if (borrowerType === 'Student') {
            courseField.classList.remove('hidden');
            yearField.classList.remove('hidden');
            positionField.classList.add('hidden');
        } else {
            courseField.classList.add('hidden');
            yearField.classList.add('hidden');
            positionField.classList.remove('hidden');
        }
    }

    // Call the function on page load to set initial state
    document.addEventListener('DOMContentLoaded', function() {
        toggleFieldsBasedOnType();
    });
</script>
</body>
</html>

<?php
$content = ob_get_clean();
include('templates/main.php');
?>
