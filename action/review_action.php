<?php
require_once '../Controllers/review_controller.php';

// Add a review
if (isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'];
    $rating = $_POST['rating'];
    $reviewText = $_POST['review_text'];

    // Instantiate the ReviewController
    $reviewController = new ReviewController();

    // Add the review
    $result = $reviewController->addReview($productId, $userId, $rating, $reviewText);

    if ($result['success']) {
        // Redirect to product page or show success message
        header("Location: product_details.php?product_id=" . $productId);
        exit();
    } else {
        echo "<script>alert('Error: " . $result['message'] . "');</script>";
    }
}

// Update a review
if (isset($_POST['update_review']) && isset($_SESSION['user_id'])) {
    $reviewId = $_POST['review_id'];
    $rating = $_POST['rating'];
    $reviewText = $_POST['review_text'];

    $reviewController = new ReviewController();
    $result = $reviewController->updateReview($reviewId, $rating, $reviewText);

    if ($result) {
        header("Location: review_details.php?review_id=" . $reviewId);
        exit();
    } else {
        echo "<script>alert('Error updating review');</script>";
    }
}

// Delete a review
if (isset($_GET['delete_review']) && isset($_SESSION['user_id'])) {
    $reviewId = $_GET['delete_review'];

    $reviewController = new ReviewController();
    $result = $reviewController->deleteReview($reviewId);

    if ($result) {
        header("Location: product_reviews.php");
        exit();
    } else {
        echo "<script>alert('Error deleting review');</script>";
    }
}
?>
