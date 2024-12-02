<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'register') {
        
    } elseif ($action == 'login') {
        $customer = new Customer();

        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($customer->login($email, $password)) {
            $_SESSION['customer_id'] = $customer_id;  
            $_SESSION['user_role'] = $user_role;      
            echo "true";
        } else {
            echo "false";
        }
    }
}
?>