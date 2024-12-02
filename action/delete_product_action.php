<?php
// File: action/delete_product_action.php
session_start();
require_once '../Controllers/products_controller.php'; 

$productsController = new ProductsController();

// Check if the product ID is provided
if (isset($_POST['delete_product'])) {
    $productId = $_POST['delete_product'];

    // Delete the product
    if ($productsController->deleteProduct($productId)) {
        $_SESSION['message'] = "Product deleted successfully!";
    } else {
        $_SESSION['message'] = "Failed to delete product. Please try again.";
    }
}

// Redirect back to the services page
header("Location: ../views/services.php");
exit();
?>
