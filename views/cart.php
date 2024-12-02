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
    $paymentStatus = verifyPayment($paymentReference); // Defined in order_action.php

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
    <link rel="stylesheet" href="../css/general.css">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #006400;
            color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header .logo {
            text-align: left;
            color: white;
        }

        header h2 {
            margin: 0;
            font-size: 2em;
            font-weight: bold;
        }

        nav ul {
            display: flex;
            justify-content: right;
            gap: 20px;
            list-style: none;
            padding: 0;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #004d00;
        }

        .motto {
            text-align: center;
            font-size: 1.5em;
            margin-top: 10px;
            color: #006400;
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #006400;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            color: #006400;
        }

        .btn-checkout {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #006400;
            color: white;
            font-size: 1.2em;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: none;
        }

        .btn-checkout:hover {
            background-color: #004d00;
        }

        .empty-cart {
            text-align: center;
            font-size: 1.2em;
        }

        .empty-cart a {
            color: #006400;
            text-decoration: none;
            font-weight: bold;
        }

        .empty-cart a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                font-size: 0.9em;
            }

            .container {
                padding: 15px;
            }

            .btn-checkout {
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="shop.php"><h2>IMENA</h2></a>
    </div>
    <nav>
        <ul>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="cart.php">View Cart</a></li>
        </ul>
    </nav>
</header>

<!-- Motto Section -->
<div class="motto">
    <p>Buy from Rwanda, Ship to the World</p>
</div>

<div class="container">
    <h1>Your Cart</h1>
    <?php if (!empty($cartItems)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (<?php echo htmlspecialchars($selectedCurrency); ?>)</th>
                    <th>Quantity</th>
                    <th>Total (<?php echo htmlspecialchars($selectedCurrency); ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0;
                $totalAmountInGHS = 0;
                foreach ($cartItems as $item):
                    $convertedPrice = $item['product_price'] * $exchangeRate;
                    $itemTotal = $convertedPrice * $item['quantity'];
                    $totalAmount += $itemTotal;

                    // Convert to GHS for Paystack
                    $priceInGHS = $item['product_price'];
                    $itemTotalInGHS = $priceInGHS * $item['quantity'];
                    $totalAmountInGHS += $itemTotalInGHS;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                    <td><?php echo number_format($convertedPrice, 2); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($itemTotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total: <?php echo htmlspecialchars($selectedCurrency) . ' ' . number_format($totalAmount, 2); ?></h3>
        <button id="pay-now" class="btn-checkout">Pay Now</button>
    <?php else: ?>
        <div class="empty-cart">
            <p>Your cart is empty. <a href="shop.php">Start shopping!</a></p>
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function () {
        $('#pay-now').click(function () {
            let handler = PaystackPop.setup({
                key: 'pk_test_fd2673b977195c947f073a83c59cb8a57e173e87',
                email: "<?php echo htmlspecialchars($_SESSION['user_email']); ?>",
                amount: <?php echo round($totalAmountInGHS * 100); ?>, // Amount in kobo/cent/paisa
                currency: "GHS",
                ref: "<?php echo uniqid('txn_'); ?>", // Generate a unique reference
                callback: function (response) {
                    // Redirect to cart.php with the payment reference
                    window.location.href = "cart.php?payment_reference=" + response.reference;
                },
                onClose: function () {
                    alert("Payment process canceled.");
                }
            });
            handler.openIframe();
        });
    });
</script>
</body>
</html>
