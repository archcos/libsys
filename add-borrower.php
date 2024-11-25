<?php
// Include your database connection file
include('db-connect.php'); // Adjust the path to your actual connection file

$conn->close();

// Start output buffering to inject this content into the main template
ob_start();

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

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 0 0 150px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            flex: 1;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .hidden {
            display: none;
        }

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

        button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function adjustFormBasedOnType(value) {
            const facultyField = document.getElementById('facultyField');
            const courseField = document.getElementById('courseField');
            const yearField = document.getElementById('yearField');
            const positionField = document.getElementById('positionField');

            if (value === 'Student') {
                facultyField.classList.add('hidden'); // Hide faculty ID
                positionField.classList.add('hidden'); // Hide position
                courseField.classList.remove('hidden'); // Show course
                yearField.classList.remove('hidden');  // Show year
            } else {
                positionField.classList.remove('hidden'); // Show position
                facultyField.classList.remove('hidden'); // Show faculty ID
                courseField.classList.add('hidden');    // Hide course
                yearField.classList.add('hidden');      // Hide year
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

    <?php if ($status === 'success'): ?>
        <!-- Success Notification -->
        <div style="background-color: #4CAF50; color: white; padding: 10px; margin-bottom: 20px; text-align: center;">
            <strong>Success!</strong> Borrower has been added successfully.
        </div>
    <?php endif; ?>

    <form method="POST" action="adding-borrower.php">
        <input type="hidden" id="borrowerType" name="borrowerType" value="<?= htmlspecialchars($borrowerType); ?>">

        <div class="form-group">
            <label for="libraryId">Library ID:</label>
            <input type="text" id="libraryId" name="libraryId" required>
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

        <!-- Depending on the borrower type (Faculty/Staff/Student), show the correct fields -->
        <div id="positionField" class="form-group hidden">
            <label for="position">Position:</label>
            <input type="text" id="position" name="position">
        </div>

        <div id="facultyField" class="form-group hidden">
            <label for="facultyId">Faculty ID:</label>
            <input type="text" id="facultyId" name="facultyId">
        </div>

        <div id="courseField" class="form-group hidden">
            <label for="course">Course:</label>
            <input type="text" id="course" name="course">
        </div>

        <div id="yearField" class="form-group hidden">
            <label for="year">Year:</label>
            <input type="number" id="year" name="year" min="1" max="5">
        </div>

        <button type="submit">Submit</button>
    </form>
</body>
</html>

<?php
// Capture the content and include it in the main template
$content = ob_get_clean();
include('templates/main.php');
?>
