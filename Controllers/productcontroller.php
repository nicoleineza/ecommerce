<?php
require_once '../classes/productclass.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    // Handle image upload and return image path
    private function handleImageUpload($imageFile) {
        if ($imageFile && isset($imageFile['error']) && $imageFile['error'] == 0) {
            $targetDir = "../uploads/images/";
            $targetFile = $targetDir . basename($imageFile["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check for valid image file types
            if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                // Attempt to move the uploaded file
                if (move_uploaded_file($imageFile["tmp_name"], $targetFile)) {
                    return 'uploads/images/' . basename($imageFile["name"]);
                } else {
                    return "Error uploading the file.";
                }
            } else {
                return "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }
        return null; // No file uploaded
    }

    // Add a product with optional image
    public function addProduct($data, $imageFile) {
        $imagePath = $this->handleImageUpload($imageFile); // Handle image upload

        // Pass data to the model to add the product
        return $this->productModel->addProduct(
            $data['seller_id'],
            $data['product_cat'],
            $data['product_title'],
            $data['product_price'],
            $data['product_desc'],
            $imagePath,
            $data['product_keywords']
        );
    }

    // Get all products
    public function getAllProducts() {
        return $this->productModel->getAllProducts();
    }

    // Get a product by its ID
    public function getProductById($product_id) {
        return $this->productModel->getProductById($product_id);
    }

    // Fetch a product by its ID and return with handling for invalid IDs
    public function fetchProductById($product_id) {
        $product = $this->getProductById($product_id);

        if (!$product) {
            return null; 
        }

        return $product;
    }

    // Update a product with optional new image
    public function updateProduct($data, $imageFile = null) {
        // Check if a new image was uploaded
        $imagePath = $this->handleImageUpload($imageFile);

        // If no new image uploaded, fetch the existing image from the database
        if (!$imagePath && isset($data['product_id'])) {
            $product = $this->productModel->getProductById($data['product_id']);
            $imagePath = $product['product_image'] ?: null;
        }

        // Update the product using the model
        return $this->productModel->updateProduct(
            $data['product_id'],
            $data['product_cat'],
            $data['product_title'],
            $data['product_price'],
            $data['product_desc'],
            $imagePath,
            $data['product_keywords']
        );
    }

    // Delete a product
    public function deleteProduct($product_id) {
        return $this->productModel->deleteProduct($product_id);
    }
    

    // Get products with optional filtering by price range and search term
    public function getProducts($minPrice = null, $maxPrice = null, $searchTerm = '') {
        if ($minPrice !== null && $maxPrice !== null && !empty($searchTerm)) {
            return $this->productModel->get_products_by_price_range_and_search($minPrice, $maxPrice, $searchTerm);
        } elseif ($minPrice !== null && $maxPrice !== null) {
            return $this->productModel->get_products_by_price_range($minPrice, $maxPrice);
        } elseif (!empty($searchTerm)) {
            return $this->productModel->get_products_by_search_term($searchTerm);
        }
        return $this->productModel->get_all_products();
    }

    // Get products by seller
    public function getProductsBySeller($sellerId) {
        return $this->productModel->getProductsBySeller($sellerId);
    }
}
?>
