<?php
//session_start(); 
require_once '../Controllers/order_controller.php';
require_once '../Controllers/cart_controller.php';
require_once '../settings/db_class.php';

// Verify the payment
function verifyPayment($paymentReference) {
    $dbInstance = new db_connection();
    $secretKey = $dbInstance->getPaystackKey();
    $url = "https://api.paystack.co/transaction/verify/" . $paymentReference;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $secretKey"
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        error_log("Curl error: " . curl_error($ch));  // Log any curl errors
        return 'failure';
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if ($result && $result['status'] && $result['data']['status'] === 'success') {
        return 'success';
    }

    return 'failure';
}

// Process order action
if (isset($_GET['payment_reference']) && isset($_SESSION['user_id'])) {
    $paymentReference = $_GET['payment_reference'];
    $userId = $_SESSION['user_id'];

    // Instantiate CartController to retrieve cart items
    $cartController = new CartController();
    $cartItems = $cartController->getCartItems($userId);

    // Calculate total amount
    $totalAmount = 0;
    foreach ($cartItems as $item) {
        $totalAmount += $item['product_price'] * $item['quantity'];
    }

    // Verify the payment
    $paymentStatus = verifyPayment($paymentReference);

    if ($paymentStatus === 'success') {
        // Instantiate OrderController
        $orderController = new OrderController(); // No need to inject dependencies now
        $orderResult = $orderController->processOrder($userId, $paymentReference, $totalAmount, $cartItems);

        if ($orderResult['success']) {
            // Empty the cart after successful order processing
            $cartController->clearCart($userId);

            // Redirect to order confirmation page
            header("Location: order_confirmation.php?orderId=" . $orderResult['orderId']);
            exit();
        } else {
            echo "<script>alert('Order processing failed: {$orderResult['message']}');</script>";
        }
    } else {
        echo "<script>alert('Payment verification failed. Please try again.');</script>";
    }
}

// Handle order status update from seller
if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $orderId = intval($_GET['order_id']); 
    $status = htmlspecialchars($_GET['status']); 

    // Validate status input
    $validStatuses = ['Accepted', 'Shipped'];
    if (!in_array($status, $validStatuses)) {
        header("Location: order_management.php?status=invalid_status");
        exit();
    }

    // Process the status update
    $orderController = new OrderController(); 
    if ($status === 'Shipped') {
        $orderController->updateOrderStatus($orderId, 'Shipped'); // Automatically update order status when all items are shipped
    } else {
        $orderController->updateOrderStatus($orderId, $status);
    }

    // Redirect back to order management page after status update
    header("Location: order_management.php?status=updated");
    exit();
}
?>
