<?php
require_once '../Controllers/productcontroller.php';

$controller = new ProductController();

// Function to add a product
function addProductAction($controller) {
    // Handle image upload if provided
    $imageFile = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $imageFile = $_FILES['product_image'];
    }

    // Prepare product data
    $data = [
        'seller_id' => $_POST['seller_id'],
        'product_cat' => $_POST['product_cat'],
        'product_title' => $_POST['product_title'],
        'product_price' => $_POST['product_price'],
        'product_desc' => $_POST['product_desc'],
        'product_keywords' => $_POST['product_keywords']
    ];

    // Call the controller to add the product
    $result = $controller->addProduct($data, $imageFile);

    // Handle result and redirect or show an error message
    if ($result === true) {
        header('Location: ../views/product.php');
        exit;
    } else {
        echo $result;
    }
}

// Function to edit a product
function editProductAction($controller) {
    // Handle image upload if a new image is provided
    $imageFile = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $imageFile = $_FILES['product_image'];
    }

    // Prepare product data
    $data = [
        'product_id' => $_POST['product_id'],
        'product_cat' => $_POST['product_cat'],
        'product_title' => $_POST['product_title'],
        'product_price' => $_POST['product_price'],
        'product_desc' => $_POST['product_desc'],
        'product_keywords' => $_POST['product_keywords']
    ];

    // Call the controller to update the product
    $result = $controller->updateProduct($data, $imageFile);

    // Handle result and redirect or show an error message
    if ($result === true) {
        header('Location: ../views/product.php');
        exit;
    } else {
        echo $result;
    }
}

// Function to delete a product
function deleteProductAction($controller) {
    // Check if 'delete_product' GET parameter exists
    if (isset($_GET['delete_product'])) {
        $product_id = $_GET['delete_product'];

        // Call the controller to delete the product by product ID
        $result = $controller->deleteProduct($product_id);

        // Redirect to product view page after deletion
        if ($result === true) {
            header('Location: ../views/product.php');
            exit;
        } else {
            echo "Error: Unable to delete product.";
        }
    } else {
        echo "Product ID not provided for deletion.";
    }
}

// Main action handler
if (isset($_POST['add_product'])) {
    addProductAction($controller); 
} elseif (isset($_POST['edit_product'])) {
    editProductAction($controller); 
} elseif (isset($_GET['delete_product'])) {
    deleteProductAction($controller); 
} else {
    echo "Invalid action.";
}
?>
