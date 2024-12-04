<?php
require_once '../settings/db_class.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = new db_connection();
    }

    // Add a product with image upload
    public function addProduct($seller_id, $product_cat, $product_title, $product_price, $product_desc, $product_image, $product_keywords) {
        // SQL query to insert product with the image path
        $sql = "INSERT INTO products (seller_id, product_cat, product_title, product_price, product_desc, product_image, product_keywords)
                VALUES ('$seller_id', '$product_cat', '$product_title', '$product_price', '$product_desc', '$product_image', '$product_keywords')";

        // Execute the query and return the result
        return $this->db->db_query($sql);
    }

    // Update a product with image upload
    public function updateProduct($product_id, $product_cat, $product_title, $product_price, $product_desc, $product_image, $product_keywords) {
        // SQL query to update the product with the new image path
        $sql = "UPDATE products 
                SET product_cat = '$product_cat', product_title = '$product_title', product_price = '$product_price', 
                    product_desc = '$product_desc', product_image = '$product_image', product_keywords = '$product_keywords'
                WHERE product_id = '$product_id'";

        // Execute the query and return the result
        return $this->db->db_query($sql);
    }

    // View all products
    public function getAllProducts() {
        $sql = "SELECT * FROM products";
        return $this->db->db_fetch_all($sql);
    }

    // View a single product
    public function getProductById($product_id) {
        $sql = "SELECT * FROM products WHERE product_id = '$product_id'";
        return $this->db->db_fetch_one($sql);
    }

    // Delete a product
    public function deleteProduct($product_id) {
        $sql = "DELETE FROM products WHERE product_id = '$product_id'";
        return $this->db->db_query($sql);
    }

    // Get products by price range and search term
    public function get_products_by_price_range_and_search($minPrice, $maxPrice, $searchTerm) {
        $searchTerm = "%" . $searchTerm . "%"; // Wildcard search for partial matches
        $sql = "SELECT * FROM products 
                WHERE product_price BETWEEN '$minPrice' AND '$maxPrice' 
                AND (product_title LIKE '$searchTerm' OR product_keywords LIKE '$searchTerm')";
        return $this->db->db_fetch_all($sql);
    }

    // Get products by price range
    public function get_products_by_price_range($minPrice, $maxPrice) {
        $sql = "SELECT * FROM products WHERE product_price BETWEEN '$minPrice' AND '$maxPrice'";
        return $this->db->db_fetch_all($sql);
    }

    // Get products by search term
    public function get_products_by_search_term($searchTerm) {
        $searchTerm = "%" . $searchTerm . "%"; 
        $sql = "SELECT * FROM products WHERE product_title LIKE '$searchTerm' OR product_keywords LIKE '$searchTerm'";
        return $this->db->db_fetch_all($sql);
    }

    // Get all products
    public function get_all_products() {
        $sql = "SELECT * FROM products";
        return $this->db->db_fetch_all($sql);
    }

    // Get products by seller ID
    public function getProductsBySeller($sellerId) {
        $sql = "SELECT * FROM products WHERE seller_id = '$sellerId'";
        return $this->db->db_fetch_all($sql);
    }

    // Update only the product description
    public function updateProductDescription($product_id, $product_desc) {
        // SQL query to update only the product description
        $sql = "UPDATE products 
                SET product_desc = '$product_desc' 
                WHERE product_id = '$product_id'";

        // Execute the query and return the result
        return $this->db->db_query($sql);
    }
}
?>
