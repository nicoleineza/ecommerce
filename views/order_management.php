<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: login.php");
    exit("You need to log in to access this page.");
}

require_once '../Controllers/order_controller.php';

$seller_id = $_SESSION['user_id']; // Seller's ID from session

$orderController = new OrderController(); 

// Handle product status update if request parameters are set
if (isset($_GET['order_id']) && isset($_GET['product_id']) && isset($_GET['status'])) {
    $order_id = $_GET['order_id'];
    $product_id = $_GET['product_id'];
    $new_status = $_GET['status'];

    // Validate status input
    $validStatuses = ['Accepted', 'Shipped', 'Cancelled', 'Completed']; // Expanded valid statuses
    if (in_array($new_status, $validStatuses)) {
        // Update product status in orderdetails
        $orderController->updateProductStatus($order_id, $product_id, $new_status);
    } else {
        echo "<script>alert('Invalid status update');</script>";
    }
}

// Fetch orders containing products sold by the seller
$orders = $orderController->fetchSellerOrders($seller_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<header>
    <h1>Order Management</h1>
    <p>Manage orders containing your products</p>
</header>

<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="product.php">Manage Products</a></li>
                <li><a href="order_management.php">Order Management</a></li>
                <a href="transaction.php">My finances</a>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h2>Orders</h2>

        <!-- Table to display orders -->
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Product Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']); ?></td>
                        <td><?= htmlspecialchars($order['order_status']); ?></td>
                        <td><?= htmlspecialchars($order['order_date']); ?></td>
                        <td><?= htmlspecialchars($order['product_title']); ?></td>
                        <td>
                            <!-- Display actions for each product in the order -->
                            <?php
                            $orderDetails = $orderController->fetchOrderDetails($order['order_id']);
                            foreach ($orderDetails as $detail): 
                            ?>
                                <!-- Show Accept button only if status is Pending -->
                                <?php if (strtolower($detail['status']) == 'pending'): ?>
                                    <a href="order_management.php?order_id=<?= $order['order_id']; ?>&product_id=<?= $detail['product_id']; ?>&status=Accepted">
                                        <button>Accept</button>
                                    </a>
                                <?php elseif (strtolower($detail['status']) == 'accepted'): ?>
                                    <!-- Show Ship button only if status is Accepted -->
                                    <a href="order_management.php?order_id=<?= $order['order_id']; ?>&product_id=<?= $detail['product_id']; ?>&status=Shipped">
                                        <button>Ship</button>
                                    </a>
                                <?php elseif (strtolower($detail['status']) == 'shipped'): ?>
                                    <!-- Show Completed button only if status is Shipped -->
                                    <a href="order_management.php?order_id=<?= $order['order_id']; ?>&product_id=<?= $detail['product_id']; ?>&status=Completed">
                                        <button>Complete</button>
                                    </a>
                                <?php elseif (strtolower($detail['status']) == 'accepted'): ?>
                                    <!-- Show Cancel button only if status is Pending -->
                                    <a href="order_management.php?order_id=<?= $order['order_id']; ?>&product_id=<?= $detail['product_id']; ?>&status=Cancelled">
                                        <button>Cancel</button>
                                    </a>
                                <?php else: ?>
                                    <p>No action available for this product.</p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>
</div>

</body>
</html>
