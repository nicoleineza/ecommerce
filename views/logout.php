<?php
session_start();

$_SESSION['message'] = 'You have successfully logged out.';


session_unset();  
session_destroy();  


header("Location: login.php");
exit();
?>
