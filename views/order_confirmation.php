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

// Fetch all orders for the customer
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
        // Redirect the user to avoid resubmission
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
        <h1>Help us improve our products by leaving a review</h1>

        <?php 
        $shippedProducts = [];
        foreach ($customerOrders as $order) {
            if (strtolower(trim($order['status'])) === 'shipped') {
                $shippedProducts[] = $order;
            }
        }
        ?>

        <?php if (!empty($shippedProducts)): ?>
            <table class="review-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Order ID</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shippedProducts as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_title']); ?>" class="product-img">
                                <?php echo htmlspecialchars($product['product_title']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['order_id']); ?></td>
                            <td>
                                <select name="rating" required>
                                    <option value="1">1 Star</option>
                                    <option value="2">2 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="5">5 Stars</option>
                                </select>
                            </td>
                            <td>
                                <textarea name="review_text" rows="2" cols="30" placeholder="share your thoughts to help us improve your experience next time..." required></textarea>
                            </td>
                            <td>
                                <form action="order_confirmation.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                    <button type="submit" name="submit_review">Submit Review</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products are ready for review.</p>
        <?php endif; ?>

        <!-- Previous Orders Section -->
        <h2>Your Previous Orders</h2>
        <?php if (!empty($customerOrders)): ?>
            <table class="previous-orders">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Invoice No</th>
                        <th>Order Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customerOrders as $order): ?>
                        <tr>
                            <td>
                                <a href="order_confirmation.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>">
                                    <?php echo htmlspecialchars($order['order_id']); ?>
                                </a>
                            </td>
                            <td>
                                <img src="<?php echo htmlspecialchars($order['product_image']); ?>" alt="<?php echo htmlspecialchars($order['product_title']); ?>" class="product-img">
                                <?php echo htmlspecialchars($order['product_title']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($order['payment_reference']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-orders">No previous orders found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Imena Mart. Luxury Products Made in Rwanda</p>
        </div>
    </footer>
</body>
</html>
