<?php
require_once '../classes/seller_class.php';

class SellerController {
    private $seller;

    public function __construct() {
        $this->seller = new Seller();
    }

    // Get seller's verification status
    public function checkVerificationStatus($userId) {
        try {
            return $this->seller->getVerificationStatus($userId);
        } catch (Exception $e) {
            error_log("Error fetching verification status for user $userId: " . $e->getMessage());
            return null;
        }
    }

    // Submit a seller verification document
    public function submitVerification($userId, $documentPath) {
        try {
            $documentType = $this->getDocumentType($documentPath);
            $this->seller->submitVerification($userId, $documentType, $documentPath);
        } catch (Exception $e) {
            error_log("Error submitting verification for user $userId: " . $e->getMessage());
        }
    }

    // Get all sellers with pending verification
    public function getPendingSellers() {
        try {
            return $this->seller->getSellersByVerificationStatus('Pending');
        } catch (Exception $e) {
            error_log("Error fetching pending sellers: " . $e->getMessage());
            return [];
        }
    }

    // Update seller's verification status (approve/reject)
    public function updateVerificationStatus($userId, $status) {
        try {
            $this->seller->updateVerificationStatus($userId, $status);
        } catch (Exception $e) {
            error_log("Error updating verification status for user $userId: " . $e->getMessage());
        }
    }

    // Add seller's story (store story in the users table)
    public function addSellerStory($userId, $content) {
        try {
            $this->seller->addSellerStory($userId, $content);
        } catch (Exception $e) {
            error_log("Error adding seller story for user $userId: " . $e->getMessage());
        }
    }

    // Get seller's store story
    public function getSellerStory($userId) {
        try {
            return $this->seller->getSellerStory($userId) ?? null;
        } catch (Exception $e) {
            error_log("Error fetching store story for user $userId: " . $e->getMessage());
            return null;
        }
    }

    // Helper function to determine the document type from MIME type
    private function getDocumentType($documentPath) {
        try {
            if (!file_exists($documentPath)) {
                throw new Exception("File does not exist at path: $documentPath");
            }

            $fileType = mime_content_type($documentPath);

            $documentTypes = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'image',
                'image/png' => 'image',
                'image/jpg' => 'image'
            ];

            return $documentTypes[$fileType] ?? 'unknown';
        } catch (Exception $e) {
            error_log("Error determining document type for $documentPath: " . $e->getMessage());
            return 'unknown';
        }
    }

    // Helper function to validate and upload a document
    public function uploadDocument($file, $targetDir) {
        try {
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new Exception("Invalid file type: $extension");
            }

            $targetPath = $targetDir . '/' . basename($file['name']);

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception("Failed to move uploaded file.");
            }

            return $targetPath;
        } catch (Exception $e) {
            error_log("Error uploading document: " . $e->getMessage());
            return null;
        }
    }
}
?>
