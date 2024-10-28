<?php
session_start(); 
require_once '../Controllers/general_controller.php'; 

// Initialize the GeneralController
$generalController = new GeneralController();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brandName = $_POST['brand_name'];

    // Check if the brand name is not empty
    if (!empty($brandName)) {
        if ($generalController->addBrand($brandName)) {
            $_SESSION['message'] = "Brand added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add brand. Please try again.";
        }
    } else {
        $_SESSION['message'] = "Brand name is required.";
    }
}

// Redirect back to the brand management page
header("Location: ../views/brand.php");
exit();
?>
