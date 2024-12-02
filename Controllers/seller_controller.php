<?php
require_once '../classes/seller_class.php';

class SellerController {
    private $seller;

    public function __construct() {
        $this->seller = new Seller();
    }

    // Get seller's verification status
    public function checkVerificationStatus($userId) {
        return $this->seller->getVerificationStatus($userId);
    }

    // Submit a seller verification document
    public function submitVerification($userId, $documentPath) {
        // Automatically determine the document type based on the file's MIME type
        $documentType = $this->getDocumentType($documentPath);

        // Call the seller class to handle the submission
        $this->seller->submitVerification($userId, $documentType, $documentPath);
    }

    // Get all sellers with pending verification
    public function getPendingSellers() {
        // Fetch sellers with verification status 'Pending', including their names
        $pendingSellers = $this->seller->getSellersByVerificationStatus('Pending');

        // Return the pending sellers as an array of seller information
        return $pendingSellers;
    }

    // Update seller's verification status (approve/reject)
    public function updateVerificationStatus($userId, $status) {
        $this->seller->updateVerificationStatus($userId, $status);
    }

    // Helper function to determine the document type from MIME type
    private function getDocumentType($documentPath) {
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
    }
}
?>
