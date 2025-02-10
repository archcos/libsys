<?php
session_start();

$successMessage = '';
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $successMessage = 'Registration successful! You can now log in using your ID Number.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>SIGN IN</title>

  <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link id="main-css-href" rel="stylesheet" href="assets/css/style.css" />

  <!-- QR Code Scanner Library -->
  <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
</head>

<body class="bg-light-gray" id="body">
  <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh">
    <div class="d-flex flex-column justify-content-between">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
          <div class="card card-default mb-0">
            <div class="card-header pb-0">
              <div class="app-brand w-100 d-flex justify-content-center border-bottom-0">
                <a class="w-auto pl-0" href="login.php">
                  <span class="brand-name text-dark" style="font-size: 20px; line-height: 1.4; text-align: center; display: block;">
                    BALUBAL LIBRARY SYSTEM
                    <span style="display: block; font-size: 14px; margin: 5px;">Borrower's Login</span>
                  </span>
                </a>
              </div>
            </div>
            <div class="card-body px-5 pb-5 pt-0">
                <form id="loginForm" method="POST">
                    <div class="row">
                        <div class="form-group col-md-12 mb-4">
                            <input type="number" class="form-control input-lg" id="idNumber" placeholder="ID Number" required />
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-success btn-pill mb-4 w-100" id="startQRScanner">
                                <i class="fas fa-qrcode"></i> Scan QR Code
                            </button>
                            <div id="qr-reader" style="width: 100%; display: none;"></div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-pill mb-4 w-100">Sign In</button>
                            <button type="button" class="btn btn-secondary btn-pill mb-4 w-100" data-toggle="modal" data-target="#addBorrowerModal">Register</button>
                            <p id="notification" class="d-none text-danger"></p>
                        </div>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qrScannerButton = document.getElementById('startQRScanner');
    const idNumberInput = document.getElementById('idNumber');
    const qrReader = document.getElementById('qr-reader');
    let html5QrcodeScanner;

    qrScannerButton.addEventListener('click', function () {
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
        }
        qrReader.style.display = "block"; // Show the scanner
        qrReader.innerHTML = ""; // Clear previous scanner UI

        html5QrcodeScanner.start(
            { facingMode: "environment" }, // Rear camera if available
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText) => {
                idNumberInput.value = decodedText; // Auto-fill ID field
                html5QrcodeScanner.stop(); // Stop scanning after success
                qrReader.style.display = "none"; // Hide scanner UI
            },
            (errorMessage) => {
                console.warn(errorMessage); // Debugging
            }
        ).catch(err => console.error("QR Scanner Error: ", err));
    });

    // Form Submission
    const loginForm = document.getElementById('loginForm');
    const notification = document.getElementById('notification');

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent form from submitting the default way

        const idNumber = idNumberInput.value;

        // Prepare form data
        const formData = new FormData();
        formData.append('idNumber', idNumber);

        try {
            const response = await fetch('process/login.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log(result); // Debugging

            if (result.success) {
                window.location.href = 'dashboard.php'; // Redirect on success
            } else {
                notification.textContent = result.message || 'Invalid ID number.';
                notification.classList.remove('d-none');
            }
        } catch (error) {
            console.log(error);
            notification.textContent = 'An error occurred. Please try again later.';
            notification.classList.remove('d-none');
        }
    });
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- QR Code Scanner Library (Ensure this is included!) -->
<script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>

</body>
</html>
