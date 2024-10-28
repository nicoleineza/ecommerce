<?php
// File: classes/products_class.php
require_once '../settings/db_class.php'; // Include database connection class

class Products extends db_connection {
    // Method to get all products
    public function get_all_products() {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, b.brand_name, c.cat_name, p.product_image, p.product_image_type
                FROM products p
                JOIN brands b ON p.product_brand = b.brand_id
                JOIN categories c ON p.product_cat = c.cat_id";
        
        return $this->db_fetch_all($sql);
    }

    // Method to add a new product with image
    public function addProduct($data, $imageData, $imageType) {
        // Escape the input to prevent SQL injection
        $title = $this->db_escape_string($data['product_title']);
        $price = floatval($data['product_price']);
        $brand = intval($data['product_brand']);
        $category = intval($data['product_cat']);

        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO products (product_title, product_price, product_brand, product_cat, product_image, product_image_type)
                VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $this->db->prepare($sql);
        // Bind the parameters (string, double, integer, integer, string, string)
        $stmt->bind_param('sdisss', $title, $price, $brand, $category, $imageData, $imageType);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Failure
        }
    }

    // Method to update a product
    public function update_product($data) {
        $id = intval($data['product_id']);
        $title = $this->db_escape_string($data['product_title']);
        $price = floatval($data['product_price']);
        $brand = intval($data['product_brand']);
        $category = intval($data['product_cat']);
        
        $sql = "UPDATE products SET 
                product_title = '$title', 
                product_price = $price,
                product_brand = $brand,
                product_cat = $category
                WHERE product_id = $id";
        
        return $this->db_query($sql);
    }

    // Method to delete a product
    public function delete_product($id) {
        $id = intval($id);
        $sql = "DELETE FROM products WHERE product_id = $id";
        return $this->db_query($sql);
    }
}
?>
