<?php

require_once '../classes/cart_class.php';
require_once '../classes/order_class.php';  

class CartController {
    private $cartModel;
    private $orderModel;

    public function __construct() {
        $this->cartModel = new Cart();
        //$this->orderModel = new Order(); /
    }

    // Retrieve items from the cart for a specific user
    public function getCartItems($userId) {
        return $this->cartModel->get_cart_items($userId);
    }

    // Add a product to the cart
    public function addToCart($productId, $userId, $quantity) {
        // Ensure quantity is a positive integer
        $quantity = max(1, intval($quantity));
        return $this->cartModel->add_to_cart($productId, $userId, $quantity);
    }

    // Update the quantity of a specific product in the cart
    public function updateCartQuantity($productId, $userId, $quantity) {
        $quantity = max(1, intval($quantity)); // Ensure positive quantity
        return $this->cartModel->update_cart_item($productId, $userId, $quantity);
    }

    // Delete an item from the cart
    public function deleteCartItem($productId, $userId) {
        return $this->cartModel->delete_cart_item($productId, $userId);
    }

    // Clear all items from the cart for a specific user (used after checkout)
    public function clearCart($userId) {
        return $this->cartModel->clear_cart($userId);
    }

    // Move cart items to the order table after successful payment
    public function moveItemsToOrder($userId) {
        // Create a new order for the user
        $orderDate = date('Y-m-d H:i:s'); 
        $status = 'pending';

        // Create a new order and get the order ID
        $orderId = $this->orderModel->create_order($userId, $orderDate, $status);
        if (!$orderId) {
            return false; // If order creation fails
        }

        // Get the cart items for the user
        $cartItems = $this->cartModel->get_cart_items($userId);
        if (empty($cartItems)) {
            return false; // No items in the cart
        }

        // Move each item to the order_items table
        foreach ($cartItems as $item) {
            $success = $this->orderModel->add_order_item($orderId, $item['product_id'], $item['quantity'], $item['product_price']);
            if (!$success) {
                return false; // Error inserting the item into orderdetails
            }
        }

        return $this->clearCart($userId); 
    }

    // Get the total amount of the cart for a specific user
    public function getCartTotal($userId) {
        return $this->cartModel->get_cart_total($userId);
    }
}

?>
