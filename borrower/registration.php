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
    <style>
        /* Styling for form */
        h1 { text-align: center; margin-bottom: 20px; }
        form {
            max-width: 600px; margin: 0 auto; padding: 20px;
            border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;
        }
        .form-group { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .form-group label { flex: 0 0 150px; font-weight: bold; }
        .form-group input, .form-group select {
            flex: 1; padding: 8px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;
        }
        .hidden { display: none; }
        .message {
            text-align: center;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
            color: white;
            max-width: 100%; /* Constrain the width */
            margin: 10px auto; /* Center the message */
        }
        .success { background-color: #4CAF50; }
        .error { background-color: #f44336; }
        button {
            display: block; width: 200px; margin: 0 auto; padding: 10px;
            background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
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
                positionField.classList.add('hidden'); // Hide position
                courseField.classList.remove('hidden'); // Show course
                yearField.classList.remove('hidden');  // Show year
            } else {
                positionField.classList.remove('hidden'); // Show position
                courseField.classList.add('hidden');    // Hide course
                yearField.classList.add('hidden');      // Hide year
                levelField.classList.add('hidden');      // Hide year
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
        <div class="message success">
            <strong>Success!</strong> Borrower has been added successfully. 
            <?php if ($latestId): ?>
                <a href="pdf/generate-pdf.php?idNumber=<?= $latestId; ?>" style="color: white; text-decoration: underline;">Generate Borrower's Card</a>
            <?php endif; ?>
        </div>
    <?php elseif ($status === 'exists'): ?>
        <div class="message error">
            <strong>Error!</strong> This ID number already exists. Please use a different one.
        </div>
    <?php elseif ($status === 'error'): ?>
        <div class="message error">
            <strong>Error!</strong> There was an issue adding the borrower. Please try again.
        </div>
    <?php endif; ?>

    <form method="POST" action="process/adding-borrower.php">
        <input type="hidden" id="borrowerType" name="borrowerType" value="<?= htmlspecialchars($borrowerType); ?>">
        <div class="form-group">
            <label for="libraryId">Library ID:</label>
            <input type="number" id="libraryId" name="libraryId" required>
        </div>
        <div class="form-group">
            <label for="idNumber">Student/Faculty ID:</label>
            <input type="number" id="idNumber" name="idNumber" required>
        </div>
        <div class="form-group">
            <label for="surName">Surname:</label>
            <input type="text" id="surName" name="surName" required>
        </div>
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>
        </div>
        <div class="form-group">
            <label for="middleName">Middle Name:</label>
            <input type="text" id="middleName" name="middleName">
        </div>
        <div class="form-group">
            <label for="emailAddress">Email Address:</label>
            <input type="text" id="emailAddress" name="emailAddress" required>
        </div>
        <div id="positionField" class="form-group hidden">
            <label for="position">Position:</label>
            <input type="text" id="position" name="position">
        </div>
        <div id="levelField" class="form-group">
            <label for="level">Level:</label>
            <select id="level" name="level">
                <option value="">Select a level</option>
                <?php while ($row = $levelsResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['level']); ?>">
                        <?= htmlspecialchars($row['level']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div id="courseField" class="form-group">
            <label for="course">Course:</label>
            <select id="course" name="courseId">
                <option value="">Select a course</option>
                <!-- Courses will be dynamically populated here -->
            </select>
        </div>
        <div id="yearField" class="form-group hidden">
            <label for="year">Year:</label>
            <input type="number" id="year" name="year" min="1">
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="birthDate">Birth Date:</label>
            <input type="date" id="birthDate" name="birthDate" required>
        </div>
        <div class="form-group">
            <label for="homeAddress">Home Address:</label>
            <input type="text" id="homeAddress" name="homeAddress">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
