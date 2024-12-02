<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register -   Imena</title>
    <link rel="stylesheet" href="../lab 1/css/register.css">
    <link rel="stylesheet" href="../lab 1/css/styles.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: whitesmoke; 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }

        .form-container {
            background-color: white; 
            color: black; 
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

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px;
            border: none; 
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); 

        }

        button {
            width: 100%; 
            padding: 10px; 
            border: none; 
            border-radius: 4px; 
            background-color: orange; 
            color: black; 
            font-size: 16px; 
            cursor: pointer; 
            transition: background-color 0.3s; 
        }

        button:hover {
            background-color: orangered; 
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
    <h2 style="color: orange;">Imena<br> Register</h2>

    <div id="message"></div>

    <div id="loading-spinner">
        <img src="../assets/loading.gif" alt="Loading..." />
    </div>

    <form id="registerForm">
        <input type="hidden" name="action" value="register">
        
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Ashesi Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="country">Country:</label>
        <input type="text" name="country" id="country" required>

        <label for="city">City:</label>
        <input type="text" name="city" id="city" required>

        <label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" required>

        <button type="submit">Register</button>
    </form>
</div>

<script src="../js/register.js"></script>

</body>
</html>
