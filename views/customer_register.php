<?php
require_once('../vendor/autoload.php'); 
require_once('../Controllers/user_controller.php');
session_start();

$error_message = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_action = new UserAction();

    if (isset($_POST['register'])) {
        // Step 1: Process registration form and send OTP
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = 'customer';

        // Validate form inputs
        if (empty($username) || empty($email) || empty($password)) {
            $error_message = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format.";
        } elseif ($user_action->getUserModel()->email_exists($email)) {
            // Access the user_model using the getter method
            $error_message = "This email is already registered.";
        } else {
            // Generate OTP and store in session
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + 600; 
            $_SESSION['registration_data'] = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
            ];

            // Send OTP to the user's email
            $user_action->sendOtpEmail($email, $otp);

            // Set session flag indicating OTP has been sent
            $_SESSION['otp_sent'] = true;
            $error_message = "An OTP has been sent to your email.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        //Verify OTP entered by the user
        $user_otp = $_POST['otp'];

        // Check if OTP has expired
        if (time() > $_SESSION['otp_expiry']) {
            unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['registration_data'], $_SESSION['otp_sent']);
            $error_message = "OTP has expired. Please request a new OTP.";
        } elseif ($user_otp == $_SESSION['otp']) {
            // OTP verified, proceed with registration
            $data = $_SESSION['registration_data'];
            $user_action->getUserModel()->register(
                $data['username'],
                $data['email'],
                $data['password'],
                $data['role']
            );

            unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['registration_data'], $_SESSION['otp_sent']);


            header("Location: login.php");
            exit();
        } else {
            $error_message = "Invalid OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imena - Customer Registration</title>
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
        <form action="customer_register.php" method="POST" class="form-container">
            <h2>Customer Registration</h2>

            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <?php if (!isset($_SESSION['otp_sent'])): ?>
                <!-- Registration Form -->
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" class="form-input" required>
                </div>

                <button type="submit" name="register" class="btn">Register</button>
            <?php else: ?>
                <!-- OTP Verification Form -->
                <div class="form-group">
                    <label for="otp">Enter OTP sent to your email:</label>
                    <input type="text" name="otp" class="form-input" required>
                </div>

                <button type="submit" name="verify_otp" class="btn">Verify OTP</button>
            <?php endif; ?>
        </form>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Imena Mart | Luxury Products Made in Rwanda</p>
        </div>
    </footer>
</body>
</html>
