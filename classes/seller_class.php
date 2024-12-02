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
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['verification_status'] : null;
    }

    // Submit a new verification document
    public function submitVerification($userId, $documentType, $documentPath) {
        $sql = "INSERT INTO seller_verification (user_id, document_type, document_path, submission_date)
                VALUES (?, ?, ?, NOW())";
        $this->db->db_query_prepared($sql, [$userId, $documentType, $documentPath]);
    }

    // Fetch all sellers with a specific verification status (e.g., 'Pending') with their names
    public function getSellersByVerificationStatus($status) {
        // Query to get distinct sellers based on the verification status
        $sql = "SELECT DISTINCT u.user_id, u.user_name, sv.verification_status, sv.document_path, sv.submission_date
                FROM seller_verification sv
                JOIN users u ON u.user_id = sv.user_id
                WHERE sv.verification_status = ?";
        
        $stmt = $this->db->db_query_prepared($sql, [$status]);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Fetch all results
    }

    // Update the seller's verification status (approve or reject)
    public function updateVerificationStatus($userId, $status) {
        $sql = "UPDATE seller_verification SET verification_status = ? WHERE user_id = ?";
        $this->db->db_query_prepared($sql, [$status, $userId]);
    }
}
?>
