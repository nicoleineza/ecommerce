<?php

require_once('../Controllers/user_controller.php');

$userAction = new UserAction();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userAction->loginUser(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imena - Login</title>
    <link rel="stylesheet" href="../css/landing.css"> 
</head>
<body>
    <header class="hero">
        <div class="container">
            <h1>Welcome to <span>Imena Mart</span></h1>
            <p>Your gateway to exclusive luxury products made in Rwanda</p>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="../views/shop.php">Shop Now!</a></li>
                <li><a href="../views/customer_register.php">Register as Customer</a></li>
                <li><a href="../views/seller_register.php">Register as Seller</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <form action="login.php" method="POST" class="form-container">
            <h2>Login to Your Account</h2>

            
            <?php
        
            session_start();

            if (isset($_SESSION['message'])) {
                echo '<p class="message">' . $_SESSION['message'] . '</p>';

                unset($_SESSION['message']);
            }
            ?>

            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-input" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-input" required placeholder="Enter your password">
            </div>

            <input type="submit" value="Login" class="btn">
        </form>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Imena Mart | Luxury Products Made in Rwanda</p>
        </div>
    </footer>
</body>
</html>
