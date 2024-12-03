<?php
require_once '../Controllers/seller_controller.php';

class SellerAction {
    private $controller;

    public function __construct() {
        $this->controller = new SellerController();
    }

    // Handle seller dashboard access
    public function handleDashboardAccess($userId) {
        // Check the seller's verification status
        $verificationStatus = $this->controller->checkVerificationStatus($userId);
        
        // Get the seller details (including user_name) for display
        $pendingSellers = $this->controller->getPendingSellers();

        // Get the seller's stories
        $sellerStories = $this->controller->getSellerStory($userId);

        // Include the dashboard view with the pending sellers and stories data
        include '../views/dashboard.php'; 
    }

    // Handle verification document submission
    public function handleVerificationSubmission($userId) {
        // Only handle POST requests and check if document is provided
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            // Ensure the uploads directory exists
            $uploadDir = '../uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
            }

            // Extract file details
            $fileTmpName = $_FILES['document']['tmp_name'];
            $fileName = basename($_FILES['document']['name']);
            $uploadPath = $uploadDir . $fileName;
            $fileSize = $_FILES['document']['size'];
            $fileType = mime_content_type($fileTmpName);

            // Allowed file types (PDF and images)
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

            // Validate the file type
            if (in_array($fileType, $allowedMimeTypes)) {
                // Move the uploaded file to the desired location
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    // Automatically determine the document type based on file type
                    $documentType = $this->getDocumentType($fileType);

                    // Submit the verification document
                    $this->controller->submitVerification($userId, $documentType, $uploadPath); 

                    // After submission, reload the dashboard
                    $this->handleDashboardAccess($userId);
                } else {
                    // Display an error if file upload fails
                    echo "Error: Unable to upload the document. Please try again.";
                }
            } else {
                // Display an error if the file type is invalid
                echo "Invalid file type. Only PDF and image files are allowed.";
            }
        }
    }

    // Handle adding a new seller story
    public function handleAddStory($userId) {
        // Check if the form is submitted with content
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
            $content = $_POST['content'];

            // Add the seller's story (no title required)
            $this->controller->addSellerStory($userId, $content);  // Store the content in the store_story field

            // After adding the story, redirect to the dashboard
            header("Location: ../views/dashboard.php");
            exit;
        }
    }

    // Helper function to determine the document type from MIME type
    private function getDocumentType($fileType) {
        // Map MIME types to document types
        $documentTypes = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'image',
            'image/png' => 'image',
            'image/jpg' => 'image'
        ];

        // Return the corresponding document type or 'unknown' if not recognized
        return $documentTypes[$fileType] ?? 'unknown';
    }
}

// Handle actions based on the request
$action = new SellerAction();
session_start(); // Start the session

// Ensure the user is logged in
$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    // Check the action in the URL
    if (isset($_GET['action']) && $_GET['action'] === 'submit_verification') {
        // Handle the verification document submission
        $action->handleVerificationSubmission($userId);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'add_story') {
        // Handle adding a new seller story
        $action->handleAddStory($userId);
    } else {
        // Otherwise, load the seller dashboard
        $action->handleDashboardAccess($userId);
    }
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit;
}
?>
