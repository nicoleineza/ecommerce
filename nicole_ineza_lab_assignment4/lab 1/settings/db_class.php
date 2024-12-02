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
    public $results = null;

    // Connect to the database
    /**
     * Connect to the database
     * @return mysqli|false - Returns mysqli connection object or false on failure
     */
    public function db_connect() {
        // If there is already a connection, return it
        if ($this->db) {
            return $this->db;
        }

        // Connect to the database
        $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

        // Check for connection errors
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            return false;
        }

        // Return the connection object
        return $this->db;
    }

    // Execute a query
    /**
     * Execute a query on the database
     * @param string $sqlQuery - The SQL query to run
     * @return boolean - Returns true on success or false on failure
     */
    public function db_query($sqlQuery) {
        // Ensure the connection is established
        $this->db_connect();

        // Execute the query
        $this->results = mysqli_query($this->db, $sqlQuery);

        // Return whether the query was successful
        return $this->results !== false;
    }

    // Execute a query with mysqli real escape string
    /**
     * Safeguard query with real escape string to prevent SQL injection
     * @param string $sqlQuery - The SQL query to run
     * @return boolean - Returns true on success or false on failure
     */
    public function db_query_escape_string($sqlQuery) {
        $this->db_connect();

        // Run the query
        $this->results = mysqli_query($this->db, $sqlQuery);

        // Return whether the query was successful
        return $this->results !== false;
    }

    // Fetch a single row from the database
    /**
     * Fetch one row from the database
     * @param string $sql - The SQL query to fetch the data
     * @return array|false - Returns an associative array of the row, or false on failure
     */
    public function db_fetch_one($sql) {
        if ($this->db_query($sql)) {
            return mysqli_fetch_assoc($this->results);
        }
        return false;
    }

    // Fetch all rows from the database
    /**
     * Fetch all rows from the database
     * @param string $sql - The SQL query to fetch the data
     * @return array|false - Returns an associative array of all rows, or false on failure
     */
    public function db_fetch_all($sql) {
        if ($this->db_query($sql)) {
            return mysqli_fetch_all($this->results, MYSQLI_ASSOC);
        }
        return false;
    }

    // Count the number of rows returned by a query
    /**
     * Count the number of rows returned by the query
     * @return int|false - Returns the number of rows or false on failure
     */
    public function db_count() {
        if ($this->results) {
            return mysqli_num_rows($this->results);
        }
        return false;
    }

    // Escape special characters in a string for use in an SQL query
    /**
     * Escape special characters to avoid SQL injection
     * @param string $string - The string to escape
     * @return string - Returns the escaped string
     */
    public function db_escape_string($string) {
        $this->db_connect();
        return mysqli_real_escape_string($this->db, $string);
    }

    // Close the database connection
    /**
     * Close the database connection
     */
    public function db_close() {
        if ($this->db) {
            mysqli_close($this->db);
            $this->db = null;
        }
    }
}
?>