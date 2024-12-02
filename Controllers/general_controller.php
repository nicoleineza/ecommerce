<?php
require_once '../classes/general_class.php'; 

class GeneralController {
    private $generalModel;

    public function __construct() {
        $this->generalModel = new general_class(); 
    }

    // Method to add a brand
    public function addBrand($brandName) {
        return $this->generalModel->add_brand($brandName); 
    }

    // Method to delete a brand
    public function deleteBrand($brandId) {
        return $this->generalModel->delete_brand($brandId); 
    }

    // Method to get all brands
    public function getBrands() {
        return $this->generalModel->get_all_brands(); 
    }

    // Method to get a brand by ID
    public function getBrandById($id) {
        return $this->generalModel->get_brand_by_id($id);
    }

    // Method to update a brand
    public function updateBrand($id, $name) {
        return $this->generalModel->update_brand($id, $name);
    }
}
?>
