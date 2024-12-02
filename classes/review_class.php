<?php
require_once '../settings/db_class.php';

class Review extends db_connection {

    // Method to add a review for a product
    public function addReview($productId, $userId, $rating, $reviewText) {
        $createdAt = date("Y-m-d H:i:s");

        $sql = "INSERT INTO reviews (product_id, user_id, rating, review_text, created_at)
                VALUES (?, ?, ?, ?, ?)";

        $params = [$productId, $userId, $rating, $reviewText, $createdAt];

        $result = $this->db_query_prepared($sql, $params);

        return $result ? $this->get_insert_id() : false;
    }

    // Method to fetch reviews for a product
    public function getProductReviews($productId) {
        $sql = "SELECT r.review_id, r.rating, r.review_text, r.created_at, u.user_name
                FROM reviews r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.product_id = ?";

        $params = [$productId];

        $result = $this->db_query_prepared($sql, $params);

        return $result ? $result->get_result()->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Method to fetch a review by review ID
    public function getReviewById($reviewId) {
        $sql = "SELECT r.review_id, r.product_id, r.user_id, r.rating, r.review_text, r.created_at, u.user_name
                FROM reviews r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.review_id = ?";

        $params = [$reviewId];

        $result = $this->db_query_prepared($sql, $params);

        return $result ? $result->get_result()->fetch_assoc() : null;
    }

    // Method to update a review
    public function updateReview($reviewId, $rating, $reviewText) {
        $sql = "UPDATE reviews SET rating = ?, review_text = ? WHERE review_id = ?";
        $params = [$rating, $reviewText, $reviewId];

        return $this->db_query_prepared($sql, $params);
    }

    // Method to delete a review
    public function deleteReview($reviewId) {
        $sql = "DELETE FROM reviews WHERE review_id = ?";
        $params = [$reviewId];

        return $this->db_query_prepared($sql, $params);
    }
}
?>
