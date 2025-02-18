<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balubal Library Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
</body>
</html>
