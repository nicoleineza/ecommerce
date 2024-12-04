<?php
require_once('../vendor/autoload.php');  // Load Composer's autoloader
require_once('../classes/user_class.php');

class UserAction {

    private $user_model;

    public function __construct() {
        $this->user_model = new User();
    }

    // Getter method for user_model
    public function getUserModel() {
        return $this->user_model;
    }

    // Register a new user or update their role (customer to seller)
    public function registerUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve form data
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $store_name = isset($_POST['store_name']) ? trim($_POST['store_name']) : null;

            // Validate input fields
            if (empty($username) || empty($email) || empty($password) || empty($role)) {
                echo "All fields are required.";
                return;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email format.";
                return;
            }

            // Check if the email already exists in the system
            if ($this->user_model->email_exists($email)) {
                // If user exists and is a customer, upgrade their role to seller
                $existing_user = $this->user_model->get_user_by_email($email);
                if ($existing_user['user_role'] == 'customer') {
                    // Update user role to 'seller'
                    $this->user_model->update_user_role($email, 'seller', $store_name);
                    echo "Your user role has been updated to Seller.";
                    header("Location: login.php"); // Redirect to login page
                    exit();
                } else {
                    echo "This email is already registered as a seller.";
                    return;
                }
            }

            // Register the new user
            $user_id = $this->user_model->register($username, $email, $password, $role, $store_name);

            // Check if registration was successful
            if ($user_id) {
                echo "Registration successful!";
                header("Location: login.php"); // Redirect to login page
                exit();
            } else {
                echo "There was an error with your registration.";
            }
        }
    }

    // Handle user login
    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve form data
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validate inputs
            if (empty($email) || empty($password)) {
                echo "Please fill in both email and password.";
                return;
            }

            // Authenticate user credentials
            $user = $this->user_model->authenticate($email, $password);

            if ($user) {
                // Start the session and store user information
                session_start();

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['user_name'] = $user['user_name'];

                // Redirect based on user role
                if ($user['user_role'] == 'customer') {
                    header("Location: ../views/shop.php"); // Redirect to customer dashboard
                    exit();
                } elseif ($user['user_role'] == 'seller') {
                    header("Location: ../views/dashboard.php"); // Redirect to seller dashboard
                    exit();
                } else {
                    header("Location: ../views/admin.php"); // Redirect to admin dashboard
                    exit();
                }
            } else {
                echo "Invalid email or password!";
            }
        }
    }

    // Send OTP email for registration verification
    public function sendOtpEmail($email, $otp) {
        // Create a new PHPMailer instance
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        try {
            // Set up the PHPMailer settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'inezanicol@gmail.com'; 
            $mail->Password = 'ktqe kdif yieo bqls'; 
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('inezanicol@gmail.com', 'Imena Support');
            $mail->addAddress($email); 
            $mail->Subject = 'Your OTP for Registration';
            $mail->Body = "Your OTP is: $otp. It is valid for 10 minutes.";

            // Send the email
            $mail->send();
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Fetch all users with the 'seller' role
    public function getUsersByRole($role) {
        return $this->user_model->get_users_by_role($role);  
    }

    // Fetch seller details by seller_id
    public function getSellerById($seller_id) {
        return $this->user_model->get_user_by_id($seller_id);  
    }
}
?>
