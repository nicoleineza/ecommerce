<?php
require_once('../vendor/autoload.php');  // Load Composer's autoloader
require_once('../Controllers/user_controller.php');
session_start();

$error_message = ""; // For displaying errors

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_action = new UserAction();

    if (isset($_POST['register'])) {
        // Step 1: Process registration form and send OTP
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $store_name = $_POST['store_name'];
        $role = 'seller';

        // Validate form inputs
        if (empty($username) || empty($email) || empty($password) || empty($store_name)) {
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
            $_SESSION['otp_expiry'] = time() + 600; // OTP expiration time (10 minutes)
            $_SESSION['registration_data'] = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'store_name' => $store_name,
                'role' => $role,
            ];

            // Send OTP to the user's email
            $user_action->sendOtpEmail($email, $otp);

            // Set session flag indicating OTP has been sent
            $_SESSION['otp_sent'] = true;
            $error_message = "An OTP has been sent to your email.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        // Step 2: Verify OTP entered by the user
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
                $data['role'],
                $data['store_name']
            );

            // Clear session data after successful registration
            unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['registration_data'], $_SESSION['otp_sent']);

            // Redirect to login page
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
    <title>Imena - Seller Registration</title>
    <link rel="stylesheet" href="../css/services.css">
</head>
<body>
    <header>
        <h1>Imena</h1>
        <p>Buy from Rwanda, Ship anywhere</p>
    </header>

    <form action="seller_register.php" method="POST">
        <h2>Seller Registration</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (!isset($_SESSION['otp_sent'])): ?>
            <!-- Registration Form -->
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <label for="store_name">Store Name:</label>
            <input type="text" name="store_name" required><br>

            <input type="hidden" name="role" value="seller"> <!-- Role is seller -->
            
            <button type="submit" name="register">Register</button>
        <?php else: ?>
            <!-- OTP Verification Form -->
            <label for="otp">Enter OTP sent to your email:</label>
            <input type="text" name="otp" required><br>

            <button type="submit" name="verify_otp">Verify OTP</button>
        <?php endif; ?>
    </form>
</body>
</html>
