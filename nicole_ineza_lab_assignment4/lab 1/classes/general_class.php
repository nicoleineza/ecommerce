<?php
require("../settings/db_class.php"); // Include your database connection class

class general_class extends db_connection
{
    // Method to add a brand
    public function add_brand($brandName) {
        $brandName = $this->db_escape_string($brandName);
        $sql = "INSERT INTO brands (brand_name) VALUES ('$brandName')";
        return $this->db_query($sql);
    }

    // Method to delete a brand by id
    public function delete_brand($id) {
        $id = intval($id); // Ensure it's an integer
        $sql = "DELETE FROM brands WHERE brand_id = $id";
        return $this->db_query($sql);
    }

    // Method to retrieve all brands
    public function get_all_brands() {
        $sql = "SELECT * FROM brands";
        return $this->db_fetch_all($sql);
    }

    // Method to get a brand by ID for editing
    public function get_brand_by_id($id) {
        $id = intval($id);
        $sql = "SELECT * FROM brands WHERE brand_id = $id";
        return $this->db_fetch_one($sql);
    }

    // Method to update a brand
    public function update_brand($id, $name) {
        $id = intval($id);
        $name = $this->db_escape_string($name);
        $sql = "UPDATE brands SET brand_name = '$name' WHERE brand_id = $id";
        return $this->db_query($sql);
    }
}
?>
