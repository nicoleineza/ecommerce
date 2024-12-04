<?php
require_once '../settings/db_class.php';

class CategoryController extends db_connection
{
    // Method to get all categories
    public function getCategories() {
        $sql = "SELECT * FROM categories";
        return $this->db_fetch_all($sql);
    }

    // Method to add a category
    public function addCategory($cat_name) {
        $cat_name = $this->db_escape_string($cat_name);
        $sql = "INSERT INTO categories (cat_name) VALUES ('$cat_name')";
        return $this->db_query($sql);
    }

    // Method to update a category
    public function updateCategory($cat_id, $cat_name) {
        $cat_name = $this->db_escape_string($cat_name);
        $sql = "UPDATE categories SET cat_name = '$cat_name' WHERE cat_id = '$cat_id'";
        return $this->db_query($sql);
    }

    // Method to delete a category
    public function deleteCategory($cat_id) {
        $sql = "DELETE FROM categories WHERE cat_id = '$cat_id'";
        return $this->db_query($sql);
    }
}
?>
