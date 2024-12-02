<?php
// Include the user controller to handle the login functionality
require_once('../Controllers/user_controller.php');

// Create an instance of the UserAction controller
$userAction = new UserAction();

// Call the loginUser method to handle the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userAction->loginUser(); // This will handle the login and redirection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imena - Login</title>
    <link rel="stylesheet" href="../css/services.css"> <!-- Ensure the path is correct -->
    <style>
        /* Style for session messages */
        .message {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .error-message {
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Imena</h1>
            <p>Buy from Rwanda, Ship anywhere</p>
        </div>
    </header>

    <main class="login-container">
        <form action="login.php" method="POST" class="login-form">
            <h2>Login to Your Account</h2>

            <!-- Display session message if it exists -->
            <?php
            // Start the session
            session_start();

            // Check if there is a session message to display
            if (isset($_SESSION['message'])) {
                echo '<p class="message">' . $_SESSION['message'] . '</p>';
                // Clear the session message after it is displayed
                unset($_SESSION['message']);
            }
            ?>

            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>

            <input type="submit" value="Login" class="btn-submit">
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Imena. All Rights Reserved.</p>
    </footer>
</body>
</html>
