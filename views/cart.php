<?php 
session_start();
require_once '../Controllers/cart_controller.php';
require_once '../action/order_action.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set default currency
$selectedCurrency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'GHS';

// Fetch exchange rates
function getExchangeRates() {
    return [
        'GHS' => 1,
        'USD' => 0.085,
        'EUR' => 0.08,
        'GBP' => 0.07,
    ];
}

$exchangeRates = getExchangeRates();
$exchangeRate = $exchangeRates[$selectedCurrency] ?? 1;

// Fetch cart items
$cartController = new CartController();
$cartItems = $cartController->getCartItems($_SESSION['user_id']);

// Handle update and delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $productId = intval($_POST['product_id']);

    if ($action === 'update') {
        $quantity = max(1, intval($_POST['quantity'])); // Ensure minimum quantity is 1
        $cartController->updateCartQuantity($productId, $_SESSION['user_id'], $quantity);
    } elseif ($action === 'delete') {
        $cartController->deleteCartItem($productId, $_SESSION['user_id']);
    }
    header("Location: cart.php");
    exit();
}

// Process payment reference if provided
if (isset($_GET['payment_reference'])) {
    $paymentReference = $_GET['payment_reference'];
    $userId = $_SESSION['user_id'];

    // Calculate total amount in GHS
    $totalAmountInGHS = 0;
    foreach ($cartItems as $item) {
        $totalAmountInGHS += $item['product_price'] * $item['quantity'];
    }

    // Verify the payment
    $paymentStatus = verifyPayment($paymentReference); 

    if ($paymentStatus === 'success') {
        // Process the order
        $orderController = new OrderController();
        $orderResult = $orderController->processOrder($userId, $paymentReference, $totalAmountInGHS, $cartItems);

        if ($orderResult['success']) {
            // Redirect to the order confirmation page
            header("Location: order_confirmation.php?orderId=" . $orderResult['orderId']);
            exit();
        } else {
            echo "<script>alert('Order processing failed: {$orderResult['message']}');</script>";
        }
    } else {
        echo "<script>alert('Payment verification failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../css/cart.css">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <style>
        .cart-product-image {
            width: 50px; 
            height: auto;
            margin-right: 10px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .quantity-controls button {
            padding: 5px 10px;
            background-color: #6a1b9a;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .cart-actions button {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="hero">
        <div class="container">
            <h1>Welcome to <span>Imena Mart</span></h1>
            <p>Your cart of luxury products from Rwanda</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">View Cart</a></li>
                <li><a href="order_confirmation.php">My Orders</a></li>
                <li><a href="logout.php">LogOut</a></li>
            </ul>
        </div>
    </nav>

    <!-- Cart Section -->
    <div class="container cart-container">
        <h2>Your Cart</h2>
        <?php if (!empty($cartItems)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price (<?php echo htmlspecialchars($selectedCurrency); ?>)</th>
                        <th>Quantity</th>
                        <th>Total (<?php echo htmlspecialchars($selectedCurrency); ?>)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalAmount = 0;
                    foreach ($cartItems as $item):
                        $convertedPrice = $item['product_price'] * $exchangeRate;
                        $itemTotal = $convertedPrice * $item['quantity'];
                        $totalAmount += $itemTotal;
                        $imagePath = !empty($item['product_image']) ? '../' . $item['product_image'] : '../assets/default-image.png';
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo $imagePath; ?>" alt="Product Image" class="cart-product-image">
                            <br>
                            <?php echo htmlspecialchars($item['product_title']); ?>
                        </td>
                        <td><?php echo number_format($convertedPrice, 2); ?></td>
                        <td>
                            <div class="quantity-controls">
                                <!-- Decrease Quantity -->
                                <form method="POST" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                    <input type="hidden" name="quantity" value="<?php echo max(1, $item['quantity'] - 1); ?>">
                                    <button type="submit">-</button>
                                </form>

                                <!-- Display Quantity -->
                                <span><?php echo htmlspecialchars($item['quantity']); ?></span>

                                <!-- Increase Quantity -->
                                <form method="POST" action="cart.php" style="display: inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                    <button type="submit">+</button>
                                </form>
                            </div>
                        </td>
                        <td><?php echo number_format($itemTotal, 2); ?></td>
                        <td>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                <button type="submit" class="cart-actions" style="background-color: #d32f2f; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Delete</button>

                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                <h3>Total: <?php echo htmlspecialchars($selectedCurrency) . ' ' . number_format($totalAmount, 2); ?></h3>
                <button id="pay-now" class="btn-checkout">Pay Now</button>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is empty. <a href="shop.php">Start shopping!</a></p>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Imena Mart | Luxury Products Made in Rwanda</p>
        </div>
    </footer>

    <script>
        document.getElementById('pay-now').addEventListener('click', function () {
            let handler = PaystackPop.setup({
                key: 'pk_test_fd2673b977195c947f073a83c59cb8a57e173e87',
                email: "<?php echo htmlspecialchars($_SESSION['user_email']); ?>",
                amount: <?php echo round($totalAmount * 100); ?>,
                currency: "GHS",
                ref: "<?php echo uniqid('txn_'); ?>", 
                callback: function (response) {
                    window.location.href = "cart.php?payment_reference=" + response.reference;
                },
                onClose: function () {
                    alert("Payment process canceled.");
                }
            });
            handler.openIframe();
        });
    </script>
</body>
</html>
