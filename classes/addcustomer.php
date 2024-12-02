<?php
require_once '../settings/db_class.php';

class Customer {
    private $db;

    public function __construct() {
        // Initialize the database connection
        $this->db = new db_connection();
    }

    // Helper function to sanitize user inputs
    public function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    // Function to add a new customer with input validation
    public function addCustomer($name, $email, $password, $country, $city, $contact, $user_role = 2) {
        // Sanitize inputs
        $name = $this->sanitizeInput($name);
        $email = $this->sanitizeInput($email);
        $password = $this->sanitizeInput($password);
        $country = $this->sanitizeInput($country);
        $city = $this->sanitizeInput($city);
        $contact = $this->sanitizeInput($contact);

        // Validate email (must be an Ashesi email)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^[a-zA-Z0-9._%+-]+@ashesi\.edu\.gh$/", $email)) {
            return json_encode(["status" => "error", "message" => "Please use a valid Ashesi email address."]);
        }

        // Validate phone number (must be in E.164 format)
        if (!preg_match("/^\+?[0-9]{10,15}$/", $contact)) {
            return json_encode(["status" => "error", "message" => "Invalid phone number format."]);
        }

        // Check if the email already exists in the database
        $mail_check = $this->db->db_fetch_one("SELECT * FROM customer WHERE customer_email = '" . $this->db->db_escape_string($email) . "'");
        if ($mail_check) {
            return json_encode(["status" => "error", "message" => "Email already exists. Please use a different email."]);
        }

        // Hash the password using a secure algorithm
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new customer into the database
        $new_customer = $this->db->db_query("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role)
                                             VALUES ('" . $this->db->db_escape_string($name) . "', '" . $this->db->db_escape_string($email) . "', '" . $hashed_password . "', 
                                                     '" . $this->db->db_escape_string($country) . "', '" . $this->db->db_escape_string($city) . "', 
                                                     '" . $this->db->db_escape_string($contact) . "', '" . (int)$user_role . "')");

        // Return success or failure message based on the database operation
        if ($new_customer) {
            return json_encode(["status" => "success", "message" => "Customer registered successfully."]);
        } else {
            return json_encode(["status" => "error", "message" => "Failed to register the customer. Please try again."]);
        }
    }

    // Function to handle customer login
    public function login($email, $password) {
        // Sanitize inputs
        $email = $this->sanitizeInput($email);
        $password = $this->sanitizeInput($password);
    
        // Fetch user details from the database by email
        $user = $this->db->db_fetch_one("SELECT * FROM customer WHERE customer_email = '" . $this->db->db_escape_string($email) . "'");
    
        if ($user) {
            // Verify the hashed password
            if (password_verify($password, $user['customer_pass'])) {
                // If password is correct, return success with user data (without password)
                return json_encode([
                    "status" => "success", 
                    "data" => [
                        'customer_id' => $user['customer_id'],
                        'customer_name' => $user['customer_name'],
                        'customer_email' => $user['customer_email'],
                        'customer_country' => $user['customer_country'],
                        'customer_city' => $user['customer_city'],
                        'customer_contact' => $user['customer_contact'],
                        'user_role' => $user['user_role']
                    ]
                ]);
            } else {
                // Incorrect password
                return json_encode(["status" => "error", "message" => "Invalid login credentials."]);
            }
        } else {
            // User not found by email
            return json_encode(["status" => "error", "message" => "Invalid login credentials."]);
        }
    }
    
}
?>
