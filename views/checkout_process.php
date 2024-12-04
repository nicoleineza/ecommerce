<?php
session_start();
require_once '../Controllers/cart_controller.php';
require_once '../Controllers/order_controller.php';

// Initialize controllers
$cartController = new CartController();
$orderController = new OrderController();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$customerId = $_SESSION['user_id'];

// Step 1: Checkout Summary (Cart Items)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['payment'])) {
    $cartItems = $cartController->getCartItems($customerId);
    $totalAmount = 0;

    foreach ($cartItems as $item) {
        $totalAmount += $item['product_price'] * $item['quantity'];
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Checkout and Payment</title>
        <link rel="stylesheet" href="../css/services.css">
    </head>
    <body>
        <header>
            <div class="logo">
                <h2><a href="index.php">mC</a></h2>
            </div>
            <nav>
                <ul>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="categories.php">Category</a></li>
                    <li><a href="services.php">Products</a></li>
                    <li><a href="brand.php">Brands</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="cart.php">View Cart</a></li>
                </ul>
            </nav>
        </header>

        <div class="container">
            <h1>Checkout Summary</h1>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                    <td>$<?php echo htmlspecialchars($item['product_price']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>$<?php echo htmlspecialchars($item['product_price'] * $item['quantity']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <h3>Total Amount: $<?php echo number_format($totalAmount, 2); ?></h3>

            <form action="checkout_and_payment.php" method="GET">
                <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
                <input type="hidden" name="customer_id" value="<?php echo $customerId; ?>">
                <button type="submit" name="payment" class="btn-checkout">Proceed to Payment</button>
            </form>
        </div>
    </body>
    </html>

<?php
} 

// Step 2: Payment Details
if (isset($_GET['payment']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $totalAmount = $_GET['total_amount'];
    $customerId = $_GET['customer_id'];

    if ($customerId != $_SESSION['user_id']) {
        header("Location: cart.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Details</title>
        <link rel="stylesheet" href="../css/services.css">
    </head>
    <body>
        <header>
            <div class="logo">
                <h2><a href="index.php">mC</a></h2>
            </div>
            <nav>
                <ul>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="categories.php">Category</a></li>
                    <li><a href="services.php">Products</a></li>
                    <li><a href="brand.php">Brands</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="cart.php">View Cart</a></li>
                </ul>
            </nav>
        </header>

        <div class="container">
            <h1>Payment Details</h1>
            <form action="checkout_and_payment.php" method="POST">
                <h3>Total Amount: $<?php echo number_format($totalAmount, 2); ?></h3>

                <label for="address">Shipping Address:</label>
                <input type="text" id="address" name="address" required>

                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>

                <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
                <input type="hidden" name="customer_id" value="<?php echo $customerId; ?>">

                <button type="submit">Submit Payment</button>
            </form>
        </div>
    </body>
    </html>

<?php
} 

// Step 3: Process Payment and Create Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $paymentMethod = $_POST['payment_method'];
    $totalAmount = $_POST['total_amount'];
    $customerId = $_POST['customer_id'];

    // Simulate payment success for now (to be integrated with a real payment gateway)
    $paymentSuccess = true;

    if ($paymentSuccess) {
        // Create order
        $orderId = $orderController->createOrder($customerId, $totalAmount, $address, $paymentMethod);

        $orderController->updateOrderStatus($orderId, 'Paid');
        $cartController->moveItemsToOrder($orderId, $customerId);
        $_SESSION['message'] = "Payment successful! Your order has been placed.";
        header("Location: checkout_and_payment.php?order_history=true");
        exit();
    } else {
        // Payment failure
        $_SESSION['message'] = "Payment failed. Please try again.";
        header("Location: checkout_and_payment.php?payment=true");
        exit();
    }
}

// Step 4: Order History
if (isset($_GET['order_history']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $customerId = $_SESSION['user_id'];
    $orders = $orderController->getOrdersByUser($customerId);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order History</title>
        <link rel="stylesheet" href="../css/services.css">
    </head>
    <body>
        <header>
            <div class="logo">
                <h2><a href="index.php">mC</a></h2>
            </div>
            <nav>
                <ul>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="categories.php">Category</a></li>
                    <li><a href="services.php">Products</a></li>
                    <li><a href="brand.php">Brands</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="cart.php">View Cart</a></li>
                </ul>
            </nav>
        </header>

        <div class="container">
            <h1>Your Order History</h1>

            <?php if (count($orders) > 0): ?>
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>You have no orders yet.</p>
            <?php endif; ?>
        </div>
    </body>
    </html>
<?php
}
?>
