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
            // Log the error and return null if something goes wrong
            error_log("Error fetching verification status for user $userId: " . $e->getMessage());
            return null;
        }
    }

    // Submit a seller verification document
    public function submitVerification($userId, $documentPath) {
        try {
            // Automatically determine the document type based on the file's MIME type
            $documentType = $this->getDocumentType($documentPath);

            // Call the seller class to handle the submission
            $this->seller->submitVerification($userId, $documentType, $documentPath);
        } catch (Exception $e) {
            // Log the error
            error_log("Error submitting verification for user $userId: " . $e->getMessage());
        }
    }

    // Get all sellers with pending verification
    public function getPendingSellers() {
        try {
            // Fetch sellers with verification status 'Pending', including their names
            $pendingSellers = $this->seller->getSellersByVerificationStatus('Pending');
            return $pendingSellers;
        } catch (Exception $e) {
            // Log the error and return an empty array
            error_log("Error fetching pending sellers: " . $e->getMessage());
            return [];
        }
    }

    // Update seller's verification status (approve/reject)
    public function updateVerificationStatus($userId, $status) {
        try {
            $this->seller->updateVerificationStatus($userId, $status);
        } catch (Exception $e) {
            // Log the error
            error_log("Error updating verification status for user $userId: " . $e->getMessage());
        }
    }

    // Add seller's story (store story in the users table)
    public function addSellerStory($userId, $content) {
        try {
            // Call the seller class to add/update the seller's story
            $this->seller->addSellerStory($userId, $content);
        } catch (Exception $e) {
            // Log the error
            error_log("Error adding seller story for user $userId: " . $e->getMessage());
        }
    }

    // Get seller's store story
    public function getSellerStory($userId) {
        try {
            // Call the seller class to fetch the store story
            $story = $this->seller->getSellerStory($userId);

            // Ensure the story is returned as an array (even if no story exists)
            return $story ?: [];
        } catch (Exception $e) {
            // Log the error and return an empty array
            error_log("Error fetching store story for user $userId: " . $e->getMessage());
            return [];
        }
    }

    // Helper function to determine the document type from MIME type
    private function getDocumentType($documentPath) {
        try {
            // Get the MIME type of the uploaded file
            $fileType = mime_content_type($documentPath);

            // Map MIME types to document types
            $documentTypes = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'image',
                'image/png' => 'image',
                'image/jpg' => 'image'
            ];

            // Return the corresponding document type or 'unknown' if not recognized
            return isset($documentTypes[$fileType]) ? $documentTypes[$fileType] : 'unknown';
        } catch (Exception $e) {
            // Log the error and return 'unknown'
            error_log("Error determining document type for $documentPath: " . $e->getMessage());
            return 'unknown';
        }
    }
}
?>
