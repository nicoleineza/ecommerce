<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../Controllers/order_controller.php';
require_once '../Controllers/review_controller.php';

$orderController = new OrderController();
$reviewController = new ReviewController();

$customerId = $_SESSION['user_id'];

// Fetch all orders for the customer
$customerOrders = $orderController->fetchUserOrders($customerId);

$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$orderDetails = $orderId ? $orderController->fetchOrderDetails($orderId) : [];

$reviewSubmitted = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {

    $productId = $_POST['product_id'];
    $rating = $_POST['rating'];
    $reviewText = $_POST['review_text'];

    //  ReviewController to handle the review submission
    $reviewResult = $reviewController->addReview($productId, $customerId, $rating, $reviewText);
    if ($reviewResult['success']) {
        $reviewSubmitted = true;
    
        header("Location: order_confirmation.php");
        exit();
    } else {
        $errorMessage = $reviewResult['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Imena Mart</title>
    <link rel="stylesheet" href="../css/order.css">
    <style>
        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            max-width: 500px;
        }
        .order-card img {
            max-width: 100px;
            margin-right: 20px;
        }
        .order-card h3 {
            margin-top: 0;
        }
        .order-card .details {
            display: flex;
            justify-content: space-between;
        }
        .order-card .details span {
            font-weight: bold;
        }
        .order-card .order-actions {
            margin-top: 10px;
        }
        .order-card .order-actions a {
            text-decoration: none;
            color: #007bff;
        }
        .review-section {
            margin-top: 20px;
        }
        .review-form {
            display: none;
            flex-direction: column;
        }
        .review-section button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .review-section select,
        .review-section textarea {
            margin-bottom: 10px;
            padding: 8px;
        }
    </style>
    <script>
        function toggleReviewForm(orderId) {
            const form = document.getElementById('review-form-' + orderId);
            const button = document.getElementById('review-button-' + orderId);
            if (form.style.display === 'none') {
                form.style.display = 'flex';
                button.textContent = 'Cancel Review';
            } else {
                form.style.display = 'none';
                button.textContent = 'Leave a Review';
            }
        }
    </script>
</head>
<body>
    <!-- Header Section -->
    <header class="hero">
        <div class="container">
            <h1>Order Confirmed! 🎉</h1>
            <p>Thank you for shopping with Imena Mart. Your order is being prepared and will be shipped soon!</p>
            <a href="../views/shop.php" class="btn">Continue Shopping</a>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content Section -->
    <div class="container main-content">
        <h1>Your Orders</h1>

        <?php if (!empty($customerOrders)): ?>
            <div class="order-cards">
                <?php foreach ($customerOrders as $order): ?>
                    <?php 
                        $imagePath = !empty($order['product_image']) ? '../' . $order['product_image'] : '../assets/default-image.png';
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($order['product_title']); ?>" class="product-img">
                            <div class="order-details">
                                <h3><?php echo htmlspecialchars($order['product_title']); ?></h3>
                                <div class="details">
                                    <span>Order ID:</span> <span><?php echo htmlspecialchars($order['order_id']); ?></span>
                                </div>
                                <div class="details">
                                    <span>Order Date:</span> <span><?php echo htmlspecialchars($order['order_date']); ?></span>
                                </div>
                                <div class="details">
                                    <span>Status:</span> <span><?php echo htmlspecialchars($order['status']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="order-actions">
                            <a href="order_confirmation.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>">View Details</a>
                        </div>

                        <!-- Review Section -->
                        <?php if (strtolower(trim($order['status'])) === 'shipped'): ?>
                            <div class="review-section">
                                <?php if ($reviewSubmitted): ?>
                                    <p>Thank you for your review!</p>
                                <?php elseif (isset($errorMessage)): ?>
                                    <p style="color: red;"><?php echo $errorMessage; ?></p>
                                <?php else: ?>
                                    <button id="review-button-<?php echo htmlspecialchars($order['order_id']); ?>" onclick="toggleReviewForm(<?php echo htmlspecialchars($order['order_id']); ?>)">
                                        Leave a Review
                                    </button>
                                    <div id="review-form-<?php echo htmlspecialchars($order['order_id']); ?>" class="review-form">
                                        <form action="order_confirmation.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($order['product_id']); ?>">
                                            <select name="rating" required>
                                                <option value="1">1 Star</option>
                                                <option value="2">2 Stars</option>
                                                <option value="3">3 Stars</option>
                                                <option value="4">4 Stars</option>
                                                <option value="5">5 Stars</option>
                                            </select>
                                            <textarea name="review_text" rows="4" placeholder="Share your thoughts..." required></textarea>
                                            <button type="submit" name="submit_review">Submit Review</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-orders">No previous orders found.</p>
        <?php endif; ?>

    </div>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Imena Mart. Luxury Products Made in Rwanda</p>
        </div>
    </footer>
</body>
</html>
