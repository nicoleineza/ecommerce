<?php
// File: controllers/ProductsController.php
require_once '../classes/products_class.php'; 

class ProductsController {
    private $productsModel;

    public function __construct() {
        $this->productsModel = new Products(); 
    }

    // Method to get all products
    public function getProducts() {
        return $this->productsModel->get_all_products(); 
    }

    // Method to add a product
    public function addProduct($data, $imageData = null, $imageType = null) {
        return $this->productsModel->addProduct($data, $imageData, $imageType);
    }

    // Method to edit a product
    public function editProduct($data) {
        return $this->productsModel->update_product($data);
    }

    // Method to delete a product
    public function deleteProduct($id) {
        return $this->productsModel->delete_product($id);
    }
}
?>
