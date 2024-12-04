<?php
session_start();
if (isset($_POST['currency'])) {
    $_SESSION['currency'] = $_POST['currency'];
}
header("Location: cart.php");
exit();
?>
