<?php
session_start();
require_once '../Controllers/cart_controller.php';
require_once '../Controllers/order_controller.php'; // Assuming you have an OrderController

$cartController = new CartController();
$orderController = new OrderController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the user ID is set after login
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Please log in to manage your cart.";
        header("Location: ../views/shop.php");
        exit();
    }

    $action = $_POST['action'];
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $userId = $_SESSION['user_id']; // Get the logged-in user ID

    if ($action === 'add') {
        $quantity = intval($_POST['quantity']);
        if ($cartController->addToCart($productId, $userId, $quantity)) {
            $_SESSION['message'] = "Product added to cart successfully!";
        } else {
            $_SESSION['message'] = "Failed to add product to cart. Please try again.";
        }
        header("Location: ../views/shop.php");
        exit();
        
    } elseif ($action === 'update') {
        $quantity = intval($_POST['quantity']);
        if ($cartController->updateCartQuantity($productId, $userId, $quantity)) {
            $_SESSION['message'] = "Cart updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update cart. Please try again.";
        }
        header("Location: ../views/cart.php");
        exit();
        
    } elseif ($action === 'delete') {
        if ($cartController->deleteCartItem($productId, $userId)) {
            $_SESSION['message'] = "Product removed from cart successfully!";
        } else {
            $_SESSION['message'] = "Failed to remove product from cart. Please try again.";
        }
        header("Location: ../views/cart.php");
        exit();
        
    } elseif ($action === 'checkout') {
        // Handle checkout by creating an order and moving items from cart to orderdetails
        $orderId = $orderController->create_order($userId,$orderDate, $status);
        
        if ($orderId) {
            $cartItems = $cartController->getCartItems($userId);
            
            foreach ($cartItems as $item) {
                $orderController->addOrderDetail($orderId, $item['p_id'], $item['qty']);
            }
            
            // Clear the cart after successful checkout
            $cartController->clearCart($userId);
            $_SESSION['message'] = "Checkout successful! Your order has been placed.";
            header("Location: ../views/order_confirmation.php");
            exit();
            
        } else {
            $_SESSION['message'] = "Checkout failed. Please try again.";
            header("Location: ../views/cart.php");
            exit();
        }
        
    } else {
        $_SESSION['message'] = "Invalid action.";
        header("Location: ../views/shop.php");
        exit();
    }
}
?>