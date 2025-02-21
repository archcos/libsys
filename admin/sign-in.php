
<?php
session_start();  // Start the session to manage session variables
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
</head>

<body class="bg-light-gray" id="body">
  <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh">
    <div class="d-flex flex-column justify-content-between">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
          <div class="card card-default mb-0">
            <div class="card-header pb-0">
              <div class="app-brand w-100 d-flex justify-content-center border-bottom-0">
                <a class="w-auto pl-0" href="sign-in.php">
                  <span class="brand-name text-dark">BALUBAL LIBRARY SYSTEM</span>
                </a>
              </div>
            </div>
            <div class="card-body px-5 pb-5 pt-0">
              <!-- Added the ID for the form -->
              <form id="loginForm" method="POST">
                <div class="row">
                  <div class="form-group col-md-12 mb-4">
                    <input type="text" class="form-control input-lg" id="username" placeholder="Username" />
                  </div>
                  <div class="form-group col-md-12 mb-4">
                    <div class="input-group">
                      <input type="password" class="form-control form-control-user" id="password" placeholder="Password" />
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="fas fa-eye" id="togglePassword"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                  <button type="submit" class="btn btn-primary" style="background-color: #007bff !important; border-color: #007bff !important;">Sign In</button>
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var passwordInput = document.getElementById('password');
      var togglePassword = document.getElementById('togglePassword');
      const loginForm = document.getElementById('loginForm');
      const notification = document.getElementById('notification');
      
      togglePassword.addEventListener('click', function () {
      console.log("Toggle clicked!");  // Check if this logs in the console
      var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

      loginForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent form from submitting the default way

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        try {
          const response = await fetch('process/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
          });

          const result = await response.json();
          if (result.success) {
            // Redirect to dashboard or another page
            window.location.href = 'dashboard.php';  // Adjust to your actual dashboard page
          } else {
            // Show error message
            notification.textContent = result.message || 'Invalid username or password.';
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
</body>
</html>
