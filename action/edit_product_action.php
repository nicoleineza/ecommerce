<?php

session_start();
require_once '../Controllers/products_controller.php';

$productsController = new ProductsController();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'product_id' => $_POST['product_id'],
        'product_title' => $_POST['product_title'],
        'product_price' => $_POST['product_price'],
        'product_brand' => $_POST['product_brand'],
        'product_cat' => $_POST['product_cat']
    ];

    // Edit the product
    if ($productsController->editProduct($data)) {
        $_SESSION['message'] = "Product updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update product. Please try again.";
    }
}

// Redirect back to the services page
header("Location: ../views/services.php");
exit();
?>
