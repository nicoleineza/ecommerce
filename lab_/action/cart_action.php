<?php
session_start();
require_once '../Controllers/cart_controller.php';

$cartController = new CartController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the customer ID is set after login
    if (!isset($_SESSION['customer_id'])) {
        $_SESSION['message'] = "Please log in to manage your cart.";
        header("Location: ../views/shop.php");
        exit();
    }

    $action = $_POST['action'];
    $productId = intval($_POST['product_id']);
    $customerId = $_SESSION['customer_id']; // Get the logged-in user ID

    if ($action === 'add') {
        $quantity = intval($_POST['quantity']);
        if ($cartController->addToCart($productId, $customerId, $quantity)) {
            $_SESSION['message'] = "Product added to cart successfully!";
        } else {
            $_SESSION['message'] = "Failed to add product to cart. Please try again.";
        }
        header("Location: ../views/shop.php");
        exit();
    } elseif ($action === 'update') {
        $quantity = intval($_POST['quantity']);
        if ($cartController->updateCartQuantity($productId, $customerId, $quantity)) {
            $_SESSION['message'] = "Cart updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update cart. Please try again.";
        }
        header("Location: ../views/cart.php");
        exit();
    } elseif ($action === 'delete') {
        if ($cartController->deleteCartItem($productId, $customerId)) {
            $_SESSION['message'] = "Product removed from cart successfully!";
        } else {
            $_SESSION['message'] = "Failed to remove product from cart. Please try again.";
        }
        header("Location: ../views/cart.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid action.";
        header("Location: ../views/shop.php");
        exit();
    }
}
?>
