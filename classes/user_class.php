<?php
require_once('../settings/db_class.php');

class User {
    private $db;

    public function __construct() {
        $this->db = new db_connection();
    }

    // Method to register a new user
    public function register($username, $email, $password, $role, $store_name = null) {
        // Sanitize input to prevent SQL injection
        $username = $this->db->db_escape_string($username);
        $email = $this->db->db_escape_string($email);
        $password = $this->db->db_escape_string($password);
        $role = $this->db->db_escape_string($role);

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query for inserting a new user into the database
        $sql = "INSERT INTO `users` (`user_name`, `user_email`, `user_password`, `user_role`, `store_name`) 
                VALUES ('$username', '$email', '$hashed_password', '$role', '$store_name')";

        // Execute the query
        $this->db->db_query($sql);

        return $this->db->get_insert_id();
    }

    // Get user data by email
    public function get_user_by_email($email) {
        $email = $this->db->db_escape_string($email);
        $sql = "SELECT * FROM `users` WHERE `user_email` = '$email'";
        return $this->db->db_fetch_one($sql);
    }

    // Get user name by user_id
    public function get_user_name_by_id($user_id) {
        $sql = "SELECT `user_name` FROM `users` WHERE `user_id` = '$user_id'";
        $result = $this->db->db_fetch_one($sql);
        return $result ? $result['user_name'] : null;  
    }

    // Fetch user data by user_id (to fetch seller details)
    public function get_user_by_id($user_id) {
        $user_id = $this->db->db_escape_string($user_id);
        $sql = "SELECT * FROM `users` WHERE `user_id` = '$user_id'";
        return $this->db->db_fetch_one($sql);
    }

    // Check if email already exists
    public function email_exists($email) {
        $email = $this->db->db_escape_string($email);
        $sql = "SELECT * FROM `users` WHERE `user_email` = '$email'";
        $result = $this->db->db_fetch_one($sql);
        return $result ? true : false;
    }

    // Update the user's role
    public function update_user_role($email, $role, $store_name = null) {
        $email = $this->db->db_escape_string($email);
        $role = $this->db->db_escape_string($role);
        $store_name = $store_name ? $this->db->db_escape_string($store_name) : null;

        // Query to update the user's role
        $sql = "UPDATE `users` SET `user_role` = '$role', `store_name` = '$store_name' WHERE `user_email` = '$email'";
        return $this->db->db_query($sql);
    }

    // Authenticate user by email and password
    public function authenticate($email, $password) {
        $email = $this->db->db_escape_string($email);
        $sql = "SELECT * FROM `users` WHERE `user_email` = '$email'";
        $user = $this->db->db_fetch_one($sql);

        if ($user && password_verify($password, $user['user_password'])) {
            // Return user data on successful authentication
            return $user;
        }

        return false; 
    }

    public function get_users_by_role($role) {
        $role = $this->db->db_escape_string($role);
        $sql = "SELECT * FROM `users` WHERE `user_role` = '$role'";
        return $this->db->db_fetch_all($sql);
    }
}
?>
