<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balubal Library Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            background-color: #FFEBB2; /* Happy and lively color */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-weight: 600;
            font-size: 2.5rem;
            color: #333;
        }
        p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 30px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .btn {
            background: white;
            padding: 40px 50px;
            font-size: 1.5rem;
            font-weight: 600;
            border: none;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: black;
            display: inline-block;
            width: 220px;
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 7px 7px 20px rgba(0, 0, 0, 0.3);
        }
        /* Social Media Section */
        .social-media {
            margin-top: 150px;
        }
        .social-media p {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 20px;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .social-icons a {
            text-decoration: none;
            font-size: 2rem;
            color: #1877F2; /* Facebook color */
            transition: transform 0.2s, color 0.2s;
        }
        .social-icons a:hover {
            transform: scale(1.2);
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Balubal Library</h1>
        <p>Choose an option to proceed:</p>
        <div class="buttons">
            <a href="books.php" class="btn">LIBRARY KIOSK</a>
            <a href="login.php" class="btn">LOGIN</a>
        </div>
    </div>

    <!-- Social Media Icons Section -->
    <div class="social-media">
        <p>VISIT US ON:</p>
        <div class="social-icons">
            <a href="https://www.facebook.com/ustpbalubal.library" target="_blank">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://www.ustp.edu.ph/cdeo/library/" target="_blank">
                <i class="fas fa-globe"></i>
            </a>
        </div>
    </div>
</body>
</html>
