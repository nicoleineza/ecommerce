<?php
session_start();
require_once '../classes/addcustomer.php';

$customer = new Customer();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json'); 
    $action = $_POST['action'];

    if ($action == 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $contact = $_POST['contact'];

        $result = $customer->addCustomer($name, $email, $password, $country, $city, $contact);
        echo $result;  
        exit;
    } if ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validate and sanitize input
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $password = filter_var($password, FILTER_SANITIZE_STRING);

        // Call the login method
        $result = $customer->login($email, $password);
        $response = json_decode($result, true);

        if ($response['status'] == "success") {
            $_SESSION['customer_id'] = $response['data']['customer_id']; // Set customer ID
            $_SESSION['customer_name'] = $response['data']['customer_name']; // Set customer name
            $_SESSION['customer_email'] = $response['data']['customer_email']; // Set customer email
            $_SESSION['user_role'] = $response['data']['user_role']; // Set user role

            // Redirect to the shop page
            header("Location: ../views/shop.php");
            exit();
        } else {
            $_SESSION['message'] = $response['message']; // Set error message
            header("Location: ../views/login.php"); // Redirect back to login
            exit();
        }
        
    
    }
}
