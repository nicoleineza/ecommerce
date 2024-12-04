<?php
require_once '../classes/order_class.php';
require_once '../classes/cart_class.php';

class OrderController {
    private $order;

    public function __construct() {
        $this->order = new Order();
    }

    // Process the order
    public function processOrder($userId, $paymentReference, $totalAmount, $cartItems) {
        // Create the order
        $orderId = $this->order->createOrder($userId, $paymentReference);

        if ($orderId) {
            // Add items to orderdetails
            $detailsResult = $this->order->createOrderDetails($orderId, $cartItems, $paymentReference);

            if ($detailsResult) {
                // Clear the cart after successful order
                $cartController = new CartController();
                $cartController->clearCart($userId);

                return [
                    'success' => true,
                    'orderId' => $orderId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add items to the order details.'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Failed to create order.'
        ];
    }

    // Fetch all orders for a user
    public function fetchUserOrders($userId) {
        return $this->order->getUserOrders($userId);
    }

    // Fetch orders for a seller
    public function fetchSellerOrders($sellerId) {
        return $this->order->getSellerOrders($sellerId);
    }

    // Fetch details of a specific order by order ID
    public function fetchOrderDetails($orderId) {
        return $this->order->getOrderDetails($orderId);
    }

    // Confirm order method
    public function confirmOrder($orderId) {
        $orderDetails = $this->fetchOrderDetails($orderId);

        if ($orderDetails) {
            return [
                'success' => true,
                'orderDetails' => $orderDetails
            ];
        }

        return [
            'success' => false,
            'message' => 'Order details not found.'
        ];
    }

    // Update order status
    public function updateOrderStatus($orderId, $status) {
        // If the status is 'Shipped', we need to check product statuses
        if ($status === 'Shipped') {
            $this->order->updateOrderStatus($orderId); // Automatically update the order status based on product statuses
        } else {
            // Otherwise, just set the order status directly
            return $this->order->setOrderStatus($orderId, $status);
        }
    }

    // Update individual product status in the order
    public function updateProductStatus($orderId, $productId, $status) {
        $result = $this->order->updateProductStatus($orderId, $productId, $status);

        // If all products are marked as 'Shipped', update the order status to 'Shipped'
        if ($status === 'Shipped') {
            $this->order->updateOrderStatus($orderId);
        }

        return $result;
    }

    // Fetch the status of a specific order
    public function fetchOrderStatus($orderId) {
        return $this->order->getOrderStatus($orderId);
    }
}
?>