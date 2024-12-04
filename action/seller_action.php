<?php
require_once '../Controllers/seller_controller.php';

class SellerAction {
    private $controller;

    public function __construct() {
        $this->controller = new SellerController();
    }

    // Handle seller dashboard access
    public function handleDashboardAccess($userId) {
        try {
            // Fetch the necessary data for the dashboard
            $verificationStatus = $this->controller->checkVerificationStatus($userId);
            $pendingSellers = $this->controller->getPendingSellers();
            $sellerStories = $this->controller->getSellerStory($userId);

            // Include the dashboard view with the data
            include '../views/dashboard.php';
        } catch (Exception $e) {
            error_log("Error loading dashboard for user $userId: " . $e->getMessage());
            echo "An error occurred while loading the dashboard. Please try again later.";
        }
    }

    // Handle verification document submission
    public function handleVerificationSubmission($userId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            try {
                $uploadDir = '../uploads/documents/';
                $this->ensureDirectoryExists($uploadDir);

                $file = $_FILES['document'];
                $fileTmpName = $file['tmp_name'];
                $fileName = basename($file['name']);
                $fileType = mime_content_type($fileTmpName);
                $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

                if (in_array($fileType, $allowedMimeTypes)) {
                    $filePath = $uploadDir . $fileName;

                    // Move the uploaded file
                    if (move_uploaded_file($fileTmpName, $filePath)) {
                        $documentType = $this->getDocumentType($fileType);
                        $this->controller->submitVerification($userId, $documentType, $filePath);

                        // Reload dashboard after successful submission
                        $this->handleDashboardAccess($userId);
                    } else {
                        throw new Exception("Failed to upload the document.");
                    }
                } else {
                    echo "Invalid file type. Only PDF and image files are allowed.";
                }
            } catch (Exception $e) {
                error_log("Error submitting verification for user $userId: " . $e->getMessage());
                echo "An error occurred while submitting the verification document.";
            }
        } else {
            echo "Invalid request. Please upload a valid document.";
        }
    }

    // Handle adding a new seller story
    public function handleAddStory($userId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
            try {
                $content = trim($_POST['content']);
                if (!empty($content)) {
                    $this->controller->addSellerStory($userId, $content);
                    header("Location: ../views/dashboard.php");
                    exit;
                } else {
                    echo "Story content cannot be empty.";
                }
            } catch (Exception $e) {
                error_log("Error adding story for user $userId: " . $e->getMessage());
                echo "An error occurred while adding the story.";
            }
        }
    }

    // Ensure the uploads directory exists
    private function ensureDirectoryExists($directory) {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true)) {
                throw new Exception("Failed to create directory: $directory");
            }
        }
    }

    // Helper function to determine the document type from MIME type
    private function getDocumentType($fileType) {
        $documentTypes = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'image',
            'image/png' => 'image',
            'image/jpg' => 'image'
        ];
        return $documentTypes[$fileType] ?? 'unknown';
    }
}

// Main execution logic
try {
    $action = new SellerAction();
    session_start();

    // Ensure the user is logged in
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        $actionType = $_GET['action'] ?? '';

        switch ($actionType) {
            case 'submit_verification':
                $action->handleVerificationSubmission($userId);
                break;
            case 'add_story':
                $action->handleAddStory($userId);
                break;
            default:
                $action->handleDashboardAccess($userId);
                break;
        }
    } else {
        // Redirect to login page if not logged in
        header("Location: ../views/login.php");
        exit;
    }
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    echo "An unexpected error occurred. Please try again later.";
}
?>
