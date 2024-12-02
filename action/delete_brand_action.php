<?php
session_start(); 
require_once '../Controllers/general_controller.php'; 

// Initialize the GeneralController
$generalController = new GeneralController();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brandId = $_POST['delete_brand'];

    // Ensure brand ID is provided
    if (!empty($brandId)) {
        if ($generalController->deleteBrand($brandId)) {
            $_SESSION['message'] = "Brand deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete brand.";
        }
    } else {
        $_SESSION['message'] = "No brand selected for deletion.";
    }
}

header("Location: ../views/brand.php");
exit();
?>
