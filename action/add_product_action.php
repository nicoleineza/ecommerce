<?php

session_start();
require_once '../Controllers/products_controller.php'; 
$productsController = new ProductsController();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'product_title' => $_POST['product_title'],
        'product_price' => $_POST['product_price'],
        'product_brand' => $_POST['product_brand'],
        'product_cat' => $_POST['product_cat']
    ];

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['product_image']['tmp_name'];
        $imageName = $_FILES['product_image']['name'];
        $imageSize = $_FILES['product_image']['size'];
        $imageType = $_FILES['product_image']['type'];

        // Optional: Validate image size and type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if ($imageSize > 5000000 || !in_array($imageType, $allowedTypes)) { // 5MB limit
            $_SESSION['message'] = "Invalid image file. Please upload a JPEG, PNG, or GIF under 5MB.";
            header("Location: ../views/services.php");
            exit();
        }

        // Read the image file into a variable
        $imageData = file_get_contents($imageTmpPath);

        // Add the product including the image data
        if ($productsController->addProduct($data, $imageData, $imageType)) {
            $_SESSION['message'] = "Product added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add product. Please try again.";
        }
    } else {
        $_SESSION['message'] = "Failed to upload image. Please try again.";
    }
}

// Redirect back to the services page
header("Location: ../views/services.php");
exit();
?>
