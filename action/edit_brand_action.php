<?php
session_start(); 
require_once '../Controllers/general_controller.php';

// Initialize the GeneralController
$generalController = new GeneralController();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brandId = $_POST['brand_id'];
    $brandName = $_POST['brand_name'];

    // Update the brand
    if ($generalController->updateBrand($brandId, $brandName)) {
        $_SESSION['message'] = "Brand updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update brand.";
    }
}

// Redirect back to the brand management page
header("Location: ../views/brand.php");
exit();
?>
