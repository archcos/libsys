<?php
session_start();

// Include your database connection file
include('db/db-connect.php');

$levelsQuery = "SELECT DISTINCT level FROM tblcourses";
$levelsResult = $conn->query($levelsQuery);

// Capture the borrowerType from the query parameter
$borrowerType = isset($_GET['borrowerType']) ? $_GET['borrowerType'] : 'Student'; // Default to Student
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Registration</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        h1 { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        form {
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px;
            border-radius: 8px; 
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .hidden { 
            display: none; 
        }
        .message {
            text-align: center;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
            color: white;
            max-width: 100%;
            margin: 10px auto;
        }
        .success { background-color: #4CAF50; }
        .error { background-color: #f44336; }
        .btn-violet {
            background-color: #6f42c1;
            color: white;
            border: none;
        }
        .btn-violet:hover {
            background-color: #5a32a3;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Populate the course dropdown based on the selected level
            $('#level').change(function () {
                const selectedLevel = $(this).val();

                if (selectedLevel) {
                    $.ajax({
                        url: 'process/fetch-courses.php',
                        type: 'POST',
                        data: { level: selectedLevel },
                        success: function (data) {
                            $('#course').html(data);
                        },
                        error: function () {
                            alert('An error occurred while fetching courses.');
                        }
                    });
                } else {
                    $('#course').html('<option value="">Select a course</option>');
                }
            });
        });

        // Function to dynamically adjust form fields based on borrower type
        function adjustFormBasedOnType(value) {
            const facultyField = document.getElementById('facultyField');
            const courseField = document.getElementById('courseField');
            const yearField = document.getElementById('yearField');
            const positionField = document.getElementById('positionField');
            const levelField = document.getElementById('levelField');

            if (value === 'Student') {
                positionField.classList.add('hidden');
                courseField.classList.remove('hidden');
                yearField.classList.remove('hidden');
            } else {
                positionField.classList.remove('hidden');
                courseField.classList.add('hidden');
                yearField.classList.add('hidden');
                levelField.classList.add('hidden');
            }
        }

        // Adjust form on page load based on pre-selected value
        document.addEventListener('DOMContentLoaded', () => {
            const borrowerType = document.getElementById('borrowerType').value;
            adjustFormBasedOnType(borrowerType);
        });
    </script>
</head>
<body>
    <h1>Borrower Registration Form - <?= htmlspecialchars($borrowerType); ?></h1>

    <?php if ($status === 'success'): 
        // Fetch the latest ID
        $query = "SELECT idNumber FROM tblborrowers ORDER BY dateRegistered DESC LIMIT 1";
        $result = $conn->query($query);
        $latestId = $result->num_rows > 0 ? $result->fetch_assoc()['idNumber'] : null;

        $conn->close();
    ?>
        <div class="alert alert-success text-center">
            Success! Borrower has been added successfully. 
            <?php if ($latestId): ?>
                <a href="pdf/generate-pdf.php?idNumber=<?= $latestId; ?>" class="text-white text-decoration-underline">Generate Borrower's Card</a>
            <?php endif; ?>
        </div>
    <?php elseif ($status === 'exists'): ?>
        <div class="alert alert-danger text-center">
            Error! This ID number already exists. Please use a different one.
        </div>
    <?php elseif ($status === 'error'): ?>
        <div class="alert alert-danger text-center">
            Error! There was an issue adding the borrower. Please try again.
        </div>
    <?php endif; ?>

    <form method="POST" action="process/adding-borrower.php">
        <input type="hidden" id="borrowerType" name="borrowerType" value="<?= htmlspecialchars($borrowerType); ?>">
        
        <div class="mb-3">
            <label for="idNumber" class="form-label">Borrower ID No:</label>
            <input type="number" class="form-control" id="idNumber" name="idNumber" required>
        </div>
        <div class="mb-3">
            <label for="surName" class="form-label">Last Name:</label>
            <input type="text" class="form-control" id="surName" name="surName" required>
        </div>
        <div class="mb-3">
            <label for="firstName" class="form-label">First Name:</label>
            <input type="text" class="form-control" id="firstName" name="firstName" required>
        </div>
        <div class="mb-3">
            <label for="middleName" class="form-label">Middle Initial:</label>
            <input type="text" class="form-control" id="middleName" name="middleName">
        </div>
        <div class="mb-3">
            <label for="emailAddress" class="form-label">Email Address:</label>
            <input type="email" class="form-control" id="emailAddress" name="emailAddress" required>
        </div>
        <div id="levelField" class="mb-3">
            <label for="level" class="form-label">Level:</label>
            <select id="level" name="level" class="form-select">
                <option value="">Select a level</option>
                <?php while ($row = $levelsResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['level']); ?>">
                        <?= htmlspecialchars($row['level']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div id="courseField" class="mb-3">
            <label for="course" class="form-label">Course:</label>
            <select id="course" name="courseId" class="form-select">
                <option value="">Select a course</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="birthDate" class="form-label">Birth Date:</label>
            <input type="date" class="form-control" id="birthDate" name="birthDate" required>
        </div>
        
        <button type="submit" class="btn btn-violet w-100">Submit</button>
        <button type="button" onclick="history.back()" class="btn btn-secondary w-100 mt-2">Back</button>
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
