<?php
// Start the session
session_start();

// Set a session message for logout
$_SESSION['message'] = 'You have successfully logged out.';

// Destroy all session data
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session

// Redirect to the login page after logout
header("Location: login.php");
exit();
?>
