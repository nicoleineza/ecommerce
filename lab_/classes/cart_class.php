<?php
// File: classes/cart_class.php
require_once '../settings/db_class.php';

class Cart extends db_connection {
    // Method to get cart items for a specific customer
    public function get_cart_items($customer_id) {
        $sql = "SELECT c.p_id, p.product_title, p.product_price, c.qty, p.product_image, p.product_image_type
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id = ?"; 

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customer_id); 
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch and return all cart items
    }

    // Method to add a product to the cart
    public function add_to_cart($productId, $customerId, $quantity) {
        // Check if the item is already in the cart
        $sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $productId, $customerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If it exists, update the quantity
            $sql = "UPDATE cart SET qty = qty + ? WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iii', $quantity, $productId, $customerId);
            return $stmt->execute();
        } else {
            // If not, insert new item
            $sql = "INSERT INTO cart (p_id, c_id, qty) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iii', $productId, $customerId, $quantity);
            return $stmt->execute();
        }
    }

    // Method to update the quantity of an item
    public function update_cart_item($productId, $customerId, $quantity) {

        if ($quantity < 1) {
            return false;
        }
        
        $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $quantity, $productId, $customerId);
        return $stmt->execute();
    }

    // Method to delete an item from the cart
    public function delete_cart_item($productId, $customerId) {
        $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $productId, $customerId);
        return $stmt->execute();
    }
}
?>
