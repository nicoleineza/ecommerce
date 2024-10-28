<?php 
// Include the database credentials
require('db_cred.php');

/**
 *@author David Sampah
 *@version 1.1
 */
class db_connection
{
    // Properties
    public $db = null;

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

    // Execute a query
    public function db_query($sqlQuery) {
        // Ensure the connection is established
        if (!$this->db) {
            $this->db_connect();
        }

        // Execute the query
        return mysqli_query($this->db, $sqlQuery);
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

    // Close the database connection
    public function db_close() {
        if ($this->db) {
            mysqli_close($this->db);
            $this->db = null;
        }
    }
}
?>
