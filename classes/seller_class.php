<?php
require_once '../settings/db_class.php';

class Seller {
    private $db;

    // Constructor to initialize the database connection
    public function __construct() {
        $this->db = new db_connection();
    }

    // Fetch seller verification status
    public function getVerificationStatus($userId) {
        $sql = "SELECT verification_status FROM seller_verification WHERE user_id = ?";
        $stmt = $this->db->db_query_prepared($sql, [$userId]);

        if ($stmt) {
            $result = $stmt->get_result()->fetch_assoc();
            return $result ? $result['verification_status'] : null;
        }
        
        // Log error if query fails
        error_log("Failed to fetch verification status for user: " . $userId);
        return null;
    }

    // Submit a new verification document
    public function submitVerification($userId, $documentType, $documentPath) {
        $sql = "INSERT INTO seller_verification (user_id, document_type, document_path, submission_date)
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->db->db_query_prepared($sql, [$userId, $documentType, $documentPath]);
        
        if (!$stmt) {
            error_log("Failed to submit verification for user: " . $userId);
        }
    }

    // Fetch all sellers with a specific verification status (e.g., 'Pending') with their names
    public function getSellersByVerificationStatus($status) {
        $sql = "SELECT DISTINCT u.user_id, u.user_name, sv.verification_status, sv.document_path, sv.submission_date
                FROM seller_verification sv
                JOIN users u ON u.user_id = sv.user_id
                WHERE sv.verification_status = ?";
        
        $stmt = $this->db->db_query_prepared($sql, [$status]);

        if ($stmt) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch all results
        }
        
        // Log error if query fails
        error_log("Failed to fetch sellers with verification status: " . $status);
        return [];
    }

    // Update the seller's verification status (approve or reject)
    public function updateVerificationStatus($userId, $status) {
        $sql = "UPDATE seller_verification SET verification_status = ? WHERE user_id = ?";
        $stmt = $this->db->db_query_prepared($sql, [$status, $userId]);

        if (!$stmt) {
            error_log("Failed to update verification status for user: " . $userId);
        }
    }

    // Add a new story for the seller (in 'store_story' column)
    public function addSellerStory($userId, $storeStory) {
        $sql = "UPDATE users SET store_story = ? WHERE user_id = ?";
        
        $stmt = $this->db->db_query_prepared($sql, [$storeStory, $userId]);
        
        if (!$stmt) {
            error_log("Failed to update seller store story for user: " . $userId);
        }
    }

    // Fetch the seller's store story
    public function getSellerStory($userId) {
        $sql = "SELECT store_story FROM users WHERE user_id = ?";
        
        $stmt = $this->db->db_query_prepared($sql, [$userId]);

        if ($stmt) {
            $result = $stmt->get_result()->fetch_assoc();
            return $result ? $result['store_story'] : null;
        }
        
        // Log error if query fails
        error_log("Failed to fetch seller store story for user: " . $userId);
        return null;
    }
}
?>
