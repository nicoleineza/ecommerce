<?php
require_once '../settings/db_class.php';

class Customer {
    private $conn;

    public function __construct() {
        $db = new db_connection();
        $this->conn = $db->db_connect();
    }

    public function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    // Add customer method with validation
    public function addCustomer($name, $email, $password, $country, $city, $contact, $user_role = 2) {
        // sanitizing the input first
        $name = $this->sanitizeInput($name);
        $email = $this->sanitizeInput($email);
        $password = $this->sanitizeInput($password);
        $country = $this->sanitizeInput($country);
        $city = $this->sanitizeInput($city);
        $contact = $this->sanitizeInput($contact);
        
        // Validate email (Ashesi email)
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@ashesi\.edu\.gh$/", $email)) {
            return json_encode(["status" => "error", "message" => "Please use a valid Ashesi email."]);
        }

        // Validate phone number in E.164 format
        if (!preg_match("/^\+?[0-9]{10,15}$/", $contact)) {
            return json_encode(["status" => "error", "message" => "Invalid phone number format."]);
        }

        // Hashing the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email exists
        $mail_check = $this->conn->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $mail_check->bind_param("s", $email);
        $mail_check->execute();
        $mail_check_result = $mail_check->get_result();

        if ($mail_check_result->num_rows > 0) {
            return json_encode(["status" => "error", "message" => "Email already exists!"]);
        }

        // Adding the customer into the database
        $new = $this->conn->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        $new->bind_param("ssssssi", $name, $email, $hashed_password, $country, $city, $contact, $user_role);

        if ($new->execute()) {
            return json_encode(["status" => "success", "message" => "Customer added successfully!"]);
        } else {
            return json_encode(["status" => "error", "message" => "Failed to register customer."]);
        }
    }

    // Login customer method
    public function login($email, $password) {
        $user = $this->conn->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $user->bind_param("s", $email);
        $user->execute();
        $user_result = $user->get_result();

        if ($user_result->num_rows > 0) {
            $row = $user_result->fetch_assoc();
            if (password_verify($password, $row['customer_pass'])) {
                // Store user ID in session
                session_start();
                $_SESSION['user_id'] = $row['customer_id']; // assuming customer_id is the primary key
                return json_encode(["status" => "success", "data" => $row]);
            } else {
                return json_encode(["status" => "error", "message" => "Incorrect password."]);
            }
        }
        return json_encode(["status" => "error", "message" => "Email not found."]);
    }
}
?>
