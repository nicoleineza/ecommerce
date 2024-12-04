<?php
require_once '../classes/review_class.php';

class ReviewController {
    private $review;

    public function __construct() {
        $this->review = new Review();
    }

    // Method to add a review for a product
    public function addReview($productId, $userId, $rating, $reviewText) {
        // Validate the rating
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating must be between 1 and 5.'];
        }

        // Add the review
        $reviewId = $this->review->addReview($productId, $userId, $rating, $reviewText);

        if ($reviewId) {
            return ['success' => true, 'reviewId' => $reviewId];
        }

        return ['success' => false, 'message' => 'Failed to add review.'];
    }

    // Method to fetch all reviews for a product
    public function fetchProductReviews($productId) {
        return $this->review->getProductReviews($productId);
    }

    // Method to fetch a review by review ID
    public function fetchReviewById($reviewId) {
        return $this->review->getReviewById($reviewId);
    }

    // Method to update a review
    public function updateReview($reviewId, $rating, $reviewText) {
        return $this->review->updateReview($reviewId, $rating, $reviewText);
    }

    // Method to delete a review
    public function deleteReview($reviewId) {
        return $this->review->deleteReview($reviewId);
    }
}
?>
