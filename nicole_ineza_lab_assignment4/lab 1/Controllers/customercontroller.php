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
    } elseif ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = $customer->login($email, $password);
        echo $result;  
        exit; 
    }
}
?>
