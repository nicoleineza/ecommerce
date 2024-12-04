<?php
// Include the database credentials
require('db_cred.php');

/**
 * Database connection class for handling all database operations.
 *
 * @author David Sampah
 * @version 1.3
 */
class db_connection
{
    // Properties
    public $db = null;

    // Paystack Private Key
    private $paystackPrivateKey = 'sk_test_d24bf5a45d2e772de1c01472ecb74f4ab60d0e04';

    public function __construct() {
        // Automatically connect to the database upon instantiation
        $this->db_connect();
    }

    // Connect to the database
    public function db_connect() {
        // If there is already a connection, return it
        if ($this->db) {
            return $this->db;
        }

        // Connect to the database
        $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

        // Check for connection errors
        if (mysqli_connect_errno()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }
    }

    // Execute a query without parameters
    public function db_query($sqlQuery) {
        // Ensure the connection is established
        if (!$this->db) {
            $this->db_connect();
        }

        // Execute the query
        return mysqli_query($this->db, $sqlQuery);
    }

    // Execute a prepared statement
    public function db_query_prepared($sql, $params) {
        // Ensure the connection is established
        if (!$this->db) {
            $this->db_connect();
        }

        // Prepare the SQL statement
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Failed to prepare SQL statement: " . $this->db->error);
        }

        // Dynamically determine types and bind parameters
        $types = '';
        foreach ($params as $param) {
            $types .= $this->get_param_type($param);
        }

        // Bind the parameters
        $stmt->bind_param($types, ...$params);

        // Execute the query
        $stmt->execute();
        return $stmt;
    }

    // Determine the correct parameter type for binding
    private function get_param_type($param) {
        if (is_int($param)) {
            return 'i'; // Integer
        } elseif (is_double($param) || is_float($param)) {
            return 'd'; // Double/Float
        } elseif (is_bool($param)) {
            return 'i'; // Treat booleans as integers
        } else {
            return 's'; // Default to string
        }
    }

    // Fetch a single row from the database
    public function db_fetch_one($sql) {
        $result = $this->db_query($sql);
        return $result ? mysqli_fetch_assoc($result) : false;
    }

    // Fetch all rows from the database
    public function db_fetch_all($sql) {
        $result = $this->db_query($sql);
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : false;
    }

    // Escape special characters in a string for use in an SQL query
    public function db_escape_string($string) {
        return mysqli_real_escape_string($this->db, $string);
    }

    // Get the last inserted ID
    public function get_insert_id() {
        if ($this->db !== null) {
            return mysqli_insert_id($this->db);
        }
        return false;
    }

    // Close the database connection
    public function db_close() {
        if ($this->db) {
            mysqli_close($this->db);
            $this->db = null;
        }
    }

    // Get Paystack Private Key
    public function getPaystackKey() {
        return $this->paystackPrivateKey;
    }

    // Additional helper methods

    // Method to fetch user details (for example, during order processing)
    public function getUserDetails($userId) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $params = [$userId];
        $result = $this->db_query_prepared($sql, $params);
        return $result ? $result->get_result()->fetch_assoc() : null;
    }

    // Method to fetch product details by product ID (for the order processing)
    public function getProductDetails($productId) {
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $params = [$productId];
        $result = $this->db_query_prepared($sql, $params);
        return $result ? $result->get_result()->fetch_assoc() : null;
    }

    // Method to fetch all orders for a specific user
    public function getOrdersByUser($userId) {
        $sql = "SELECT * FROM orders WHERE user_id = ?";
        $params = [$userId];
        $result = $this->db_query_prepared($sql, $params);
        return $result ? $result->get_result()->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
