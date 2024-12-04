<?php
session_start(); // Start the session

require_once '../settings/db_class.php';

// Ensure user is logged in as a seller
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'seller') {
    echo "You must be logged in as a seller to view your transaction history.";
    exit;
}

class TransactionHistory {
    private $db;

    public function __construct() {
        $this->db = new db_connection();
    }

    // Get transaction history for the logged-in seller
    public function getTransactionHistory() {
        $user_id = $_SESSION['user_id']; // Fetch user_id from session (this is the logged-in seller's ID)

        // SQL query to fetch all shipped products for the given seller
        $sql = "
            SELECT 
                o.order_id,
                o.order_date,
                od.product_id,
                p.product_title,
                od.quantity,
                p.product_price,
                (od.quantity * p.product_price) AS total_price,
                o.payment_reference,
                o.order_status
            FROM orders o
            JOIN orderdetails od ON o.order_id = od.order_id
            JOIN products p ON od.product_id = p.product_id
            WHERE p.seller_id = '$user_id' 
                AND o.order_status = 'Shipped'
        ";

        // Fetch the data
        $results = $this->db->db_fetch_all($sql);
        $totalRevenue = 0;

        // Calculate the total revenue
        foreach ($results as $result) {
            $totalRevenue += $result['total_price'];
        }

        // Display the transaction history and total revenue
        echo "<h3>Track your Transactions here</h3>";
        echo "<table class='transaction-table'>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Product Title</th>
                        <th>Quantity</th>
                        <th>Product Price</th>
                        <th>Total Price</th>
                        <th>Payment Reference</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($results as $result) {
            echo "<tr>
                    <td>{$result['order_id']}</td>
                    <td>{$result['order_date']}</td>
                    <td>{$result['product_title']}</td>
                    <td>{$result['quantity']}</td>
                    <td>{$result['product_price']}</td>
                    <td>{$result['total_price']}</td>
                    <td>{$result['payment_reference']}</td>
                    <td>{$result['order_status']}</td>
                </tr>";
        }

        echo "</tbody></table>";
        echo "<h4 class='total-revenue'>Total Revenue: $totalRevenue</h4>";
    }
}

// Example usage
$transactionHistory = new TransactionHistory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Imena Mart</title>
    <link rel="stylesheet" href="../css/transaction.css"> <!-- Link to your existing CSS file -->
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
    <a href="transaction.php">My finances</a>
        <a href="order_management.php">Order Management</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="product.php">Product Management</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Hero Section -->
            <header class="hero">
                <h1>Welcome to <span>Imena Mart</span></h1>
                <p>Your go-to platform for luxury Rwandan products, from clothes to accessories.</p>
            </header>

            <!-- Transaction History Section -->
            <?php
                $transactionHistory->getTransactionHistory();
            ?>
        </div>
    </div>
</body>
</html>
