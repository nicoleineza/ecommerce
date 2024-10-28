<?php

require_once '../classes/cart_class.php'; 

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new Cart(); 
    }

    // Retrieve items from the cart for a specific customer
    public function getCartItems($customerId) {
        return $this->cartModel->get_cart_items($customerId);
    }

    // Add a product to the cart
    public function addToCart($productId, $customerId, $quantity) {
        // Ensure quantity is a positive integer
        $quantity = max(1, intval($quantity));
        return $this->cartModel->add_to_cart($productId, $customerId, $quantity);
    }

    // Update the quantity of a specific product in the cart
    public function updateCartQuantity($productId, $customerId, $quantity) {
        // Ensure quantity is a positive integer
        $quantity = max(1, intval($quantity));
        return $this->cartModel->update_cart_item($productId, $customerId, $quantity);
    }

    // Delete an item from the cart
    public function deleteCartItem($productId, $customerId) {
        return $this->cartModel->delete_cart_item($productId, $customerId);
    }
}
?>
