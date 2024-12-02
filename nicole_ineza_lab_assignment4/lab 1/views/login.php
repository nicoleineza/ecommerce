<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyCraft</title>
    <link rel="stylesheet" href="../lab 1/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }

        .form-container {
            background-color: black; 
            color: white; 
            padding: 20px;
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); 
            width: 400px; 
        }

        h2 {
            text-align: center; 
        }

        label {
            display: block; 
            margin-bottom: 5px; 
        }

        input[type="email"], input[type="password"] {
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: none; 
            border-radius: 4px; 
        }

        button {
            width: 100%;
            padding: 10px; 
            border: none; 
            border-radius: 4px; 
            background-color: #007BFF; 
            color: white; 
            font-size: 16px; 
            cursor: pointer; 
            transition: background-color 0.3s; 
        }

        button:hover {
            background-color: #0056b3;
        }

        #message {
            display: none; 
            padding: 10px; 
            margin-top: 10px; 
        }

        #message.success { 
            color: green; 
        }

        #message.error { 
            color: red; 
        }

        #loading-spinner { 
            display: none; 
        }

        .register-link {
            display: block; 
            text-align: center; 
            margin-top: 15px; 
            color: white; 
            text-decoration: none; 
        }

        .register-link:hover {
            text-decoration: underline; 
        }
    </style>
</head>
<body>

<!-- Header -->
<header>
    <div class="nav-bar">
        <span class="website-name"></span>
        
    </div>
</header>

<div class="form-container">
    <h2>MyCraft Login</h2>

    <div id="message"></div>

    <div id="loading-spinner">
        <img src="../assets/loading.gif" alt="Loading..." />
    </div>

    <form id="loginForm">
        <input type="hidden" name="action" value="login">
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>

    <a href="../lab 1/views/register.php" class="register-link">Don't have an account? Register here</a>
</div>

<script src="../js/login.js"></script>

</body>
</html>
