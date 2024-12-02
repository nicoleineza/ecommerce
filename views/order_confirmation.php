<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../controllers/order_controller.php';
require_once '../controllers/review_controller.php';

$orderController = new OrderController();
$reviewController = new ReviewController();

$customerId = $_SESSION['user_id'];

// Fetching all orders for the customer
$customerOrders = $orderController->fetchUserOrders($customerId);

// Check if an order ID is provided for detailed view
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$orderDetails = $orderId ? $orderController->fetchOrderDetails($orderId) : [];

$reviewSubmitted = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    // Get the review details from the form
    $productId = $_POST['product_id'];
    $rating = $_POST['rating'];
    $reviewText = $_POST['review_text'];

    // Use the ReviewController to handle the review submission
    $reviewResult = $reviewController->addReview($productId, $customerId, $rating, $reviewText);
    if ($reviewResult['success']) {
        $reviewSubmitted = true;
        // Redirect the user to previous orders after review submission
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
    <title>Order Review</title>
    <link rel="stylesheet" href="../css/order.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <h2>IMENA</h2>
            <h3>Made in Rwanda, Ship to the World</h3>
        </div>
        <nav>
            <ul>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Section -->
    <div class="container main-content">
        <h1>Write a Review</h1>

        <!-- Review Section -->
        <?php if ($orderId): ?>
            <h2>Review Your Order (Order ID: <?php echo htmlspecialchars($orderId); ?>)</h2>

            <?php if (!empty($orderDetails)): ?>
                <table class="order-summary">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                    </tr>

                    <?php foreach ($orderDetails as $detail): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($detail['product_image']); ?>" alt="<?php echo htmlspecialchars($detail['product_title']); ?>">
                                <?php echo htmlspecialchars($detail['product_title']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Check if order is shipped and show review button -->
                <?php
                $orderStatus = strtolower(trim($orderDetails[0]['status']));
                if ($orderStatus == 'shipped'): ?>
                    <button id="reviewButton" onclick="toggleReviewForm()">Rate and Review Your Products</button>

                    <div id="reviewForm" style="display:none;">
                        <h3>Rate and Review Your Products</h3>

                        <!-- Review Form -->
                        <form action="order_confirmation.php?order_id=<?php echo htmlspecialchars($orderId); ?>" method="POST">
                            <label for="product_id">Product:</label>
                            <select name="product_id" required>
                                <?php foreach ($orderDetails as $detail): ?>
                                    <option value="<?php echo $detail['product_id']; ?>"><?php echo htmlspecialchars($detail['product_title']); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label for="rating">Rating:</label>
                            <select name="rating" required>
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>

                            <label for="review_text">Review:</label>
                            <textarea name="review_text" rows="4" cols="50" placeholder="Write your review here..."></textarea>

                            <button type="submit" name="submit_review">Submit Review</button>
                        </form>

                        <?php if ($reviewSubmitted): ?>
                            <p>Thank you for your review!</p>
                        <?php elseif (isset($errorMessage)): ?>
                            <p class="error-message"><?php echo $errorMessage; ?></p>
                        <?php endif; ?>
                    </div>

                    <script>
                        // JavaScript function to toggle the review form
                        function toggleReviewForm() {
                            var reviewForm = document.getElementById('reviewForm');
                            if (reviewForm.style.display === 'none') {
                                reviewForm.style.display = 'block';
                            } else {
                                reviewForm.style.display = 'none';
                            }
                        }
                    </script>

                <?php else: ?>
                    <p>The order has not been shipped yet. Review will be available once shipped.</p>
                <?php endif; ?>

            <?php else: ?>
                <p>No items found in this order.</p>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Previous Orders Section -->
        <h2>Your Previous Orders</h2>
        <?php if (!empty($customerOrders)): ?>
            <table class="previous-orders">
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Invoice No</th>
                    <th>Order Date</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($customerOrders as $order): ?>
                    <tr>
                        <td>
                            <a href="order_confirmation.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>">
                                <?php echo htmlspecialchars($order['order_id']); ?>
                            </a>
                        </td>
                        <td>
                            <img src="<?php echo htmlspecialchars($order['product_image']); ?>" alt="<?php echo htmlspecialchars($order['product_title']); ?>">
                            <?php echo htmlspecialchars($order['product_title']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['payment_reference']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="no-orders">No previous orders found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 MyShop. All Rights Reserved.</p>
    </footer>
</body>
</html>
