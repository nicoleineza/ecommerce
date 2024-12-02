<?php
require_once '../settings/db_class.php';

class Order extends db_connection {

    // Method to create an order
    public function createOrder($userId, $paymentReference) {
        $orderDate = date("Y-m-d H:i:s");

        // Insert order into the orders table
        $sql = "INSERT INTO orders (user_id, payment_reference, order_date, order_status)
                VALUES (?, ?, ?, ?)";
        $params = [$userId, $paymentReference, $orderDate, 'Pending']; // Default order status as 'Pending'

        $orderResult = $this->db_query_prepared($sql, $params);

        // Return the newly created order ID
        if ($orderResult) {
            return $this->get_insert_id();
        }

        return false;
    }

    // Method to create order details (items in the order)
    public function createOrderDetails($orderId, $cartItems, $paymentReference) {
        $sql = "INSERT INTO orderdetails (order_id, product_id, quantity, payment_reference, status)
                VALUES (?, ?, ?, ?, ?)";  // Changed product_status to status

        $allSuccessful = true;

        foreach ($cartItems as $item) {
            $params = [
                $orderId, 
                $item['product_id'], 
                $item['quantity'], 
                $paymentReference,
                'Pending' // Default product status
            ];
            $result = $this->db_query_prepared($sql, $params);
            if (!$result) {
                $allSuccessful = false;
            }
        }

        return $allSuccessful;
    }

    // Fetch orders for a user, with product information and statuses
    public function getUserOrders($userId) {
        $sql = "SELECT o.order_id, o.payment_reference, o.order_date, o.order_status, 
                       od.product_id, p.product_title, p.product_image, od.status  -- Changed product_status to status
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE o.user_id = ?";

        $params = [$userId];

        $result = $this->db_query_prepared($sql, $params);

        return $result ? $result->get_result()->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Fetch orders for a seller, with product information and statuses
    public function getSellerOrders($sellerId) {
        $sql = "SELECT o.order_id, o.payment_reference, o.order_date, o.order_status, 
                       od.product_id, p.product_title, p.product_image, od.status  -- Changed product_status to status
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE p.seller_id = ?";

        $params = [$sellerId];

        $result = $this->db_query_prepared($sql, $params);

        return $result ? $result->get_result()->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Fetch all details for a specific order
    public function getOrderDetails($orderId) {
        $sql = "SELECT od.*, p.product_title, p.product_image 
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";

        $params = [$orderId];

        $result = $this->db_query_prepared($sql, $params);

        return $result ? $result->get_result()->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Update the status of a single product within an order
    public function updateProductStatus($orderId, $productId, $status) {
        $sql = "UPDATE orderdetails SET status = ? WHERE order_id = ? AND product_id = ?";  // Changed product_status to status
        $params = [$status, $orderId, $productId];
        return $this->db_query_prepared($sql, $params);
    }

    // Update the status of the entire order based on the statuses of the products
    public function updateOrderStatus($orderId) {
        // Check if all products in the order are marked as 'Shipped'
        $sql = "SELECT COUNT(*) AS total_products, 
                       SUM(CASE WHEN status != 'Shipped' THEN 1 ELSE 0 END) AS pending_products  -- Changed product_status to status
                FROM orderdetails
                WHERE order_id = ?";
        $params = [$orderId];
        $result = $this->db_query_prepared($sql, $params);

        if ($result) {
            $data = $result->get_result()->fetch_assoc();
            if ($data['pending_products'] == 0) {  // All products are shipped
                // Set the order status to 'Shipped'
                $this->setOrderStatus($orderId, 'Shipped');
            }
        }
    }

    // Method to set or update the order status directly
    public function setOrderStatus($orderId, $status) {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $params = [$status, $orderId];
        return $this->db_query_prepared($sql, $params);
    }

    // Get a single order's status
    public function getOrderStatus($orderId) {
        $sql = "SELECT order_status FROM orders WHERE order_id = ?";
        $params = [$orderId];
        $result = $this->db_query_prepared($sql, $params);
        return $result ? $result->get_result()->fetch_assoc()['order_status'] : null;
    }
}
?>
