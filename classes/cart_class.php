<?php
// File: classes/cart_class.php
require_once '../settings/db_class.php';

class Cart extends db_connection {
    // Method to get cart items for a specific user
    public function get_cart_items($user_id) {
        $sql = "SELECT c.cart_id, c.product_id, p.product_title, p.product_price, c.quantity, p.product_image
                FROM cart c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = ?"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        
        // Fetch and return all cart items as an associative array
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); 
    }

    // Method to add a product to the cart
    public function add_to_cart($product_id, $user_id, $quantity) {
        // Check if the item is already in the cart
        $sql = "SELECT * FROM cart WHERE product_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $product_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the product exists in the cart, update the quantity
            $sql = "UPDATE cart SET quantity = quantity + ? WHERE product_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iii', $quantity, $product_id, $user_id);
            return $stmt->execute();
        } else {
            // If not, insert a new item into the cart
            $sql = "INSERT INTO cart (product_id, user_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iii', $product_id, $user_id, $quantity);
            return $stmt->execute();
        }
    }

    // Method to update the quantity of an item in the cart
    public function update_cart_item($product_id, $user_id, $quantity) {
        if ($quantity < 1) {
            return false; // Prevent setting quantity to less than 1
        }

        // Update the quantity of the cart item
        $sql = "UPDATE cart SET quantity = ? WHERE product_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $quantity, $product_id, $user_id);
        return $stmt->execute();
    }

    // Method to delete an item from the cart
    public function delete_cart_item($product_id, $user_id) {
        $sql = "DELETE FROM cart WHERE product_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $product_id, $user_id);
        return $stmt->execute();
    }

    // Method to clear all items from the cart for a specific user (used after checkout)
    public function clear_cart($user_id) {
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $user_id);
        return $stmt->execute();
    }

    // Method to calculate the total price of the selected cart items
    public function get_cart_total($user_id) {
        // Calculate the total amount by summing the product price * quantity for all items in the cart
        $sql = "SELECT SUM(p.product_price * c.quantity) AS total_amount
                FROM cart c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch the total amount from the result and return it
        $total = $result->fetch_assoc();
        return $total ? $total['total_amount'] : 0; // Return 0 if no items found
    }
}
?>
