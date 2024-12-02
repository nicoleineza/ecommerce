<?php
session_start();

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: login.php");
    exit("You need to log in to access this page.");
}

require_once '../Controllers/order_controller.php';
require_once '../Controllers/productcontroller.php';
require_once '../Controllers/seller_controller.php';  // Include the seller controller to check verification status

$seller_id = $_SESSION['user_id']; // Seller's ID from session

// Instantiate controllers
$orderController = new OrderController();
$productController = new ProductController();
$sellerController = new SellerController();

// Fetch verification status
$verificationStatus = $sellerController->checkVerificationStatus($seller_id);

// Initialize statistics variables
$totalOrders = 0;
$pendingOrders = 0;
$totalProducts = 0;

// Fetch statistics only if verified
if ($verificationStatus === 'Approved') {
    $totalOrders = $orderController->fetchSellerOrders($seller_id);
    $pendingOrders = $orderController->fetchSellerOrders($seller_id); // Adjust according to your method that fetches pending orders
    $totalProducts = count($productController->getAllProducts()); // Adjust based on how you want to fetch the total products
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/general.css">
</head>
<body>

<header>
    <h1>Dashboard</h1>
    <p>Overview of your seller account</p>
</header>

<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <?php if ($verificationStatus === 'Approved'): ?>
                    <li><a href="product.php">Manage Products</a></li>
                    <li><a href="order_management.php">Order Management</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h2>

        <!-- Verification Status -->
        <section class="verification-status">
            <h3>Verification Status: <?= htmlspecialchars($verificationStatus); ?></h3>
            <?php if ($verificationStatus === 'Pending'): ?>
                <p>Your verification is currently pending. You cannot submit new documents until the current verification process is completed.</p>
            <?php elseif ($verificationStatus !== 'Approved'): ?>
                <p>Your account is not approved yet. Please submit your verification document below.</p>
                <p>Accepted document types: PDF, PNG, JPG, JPEG</p>
                <form action="../action/seller_action.php?action=submit_verification" method="POST" enctype="multipart/form-data">
                    <label for="document">Upload Document:</label>
                    <input type="file" name="document" accept=".pdf,image/*" required>
                    <br><br>
                    <button type="submit">Submit for Verification</button>
                </form>
            <?php else: ?>
                <p>Your verification is approved. You can now manage your products and orders.</p>
            <?php endif; ?>
        </section>

        <!-- Statistics Section (Only visible if verified) -->
        <?php if ($verificationStatus === 'Approved'): ?>
            <section class="statistics">
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p><?= htmlspecialchars(count($totalOrders)); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <p><?= htmlspecialchars(count($pendingOrders)); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p><?= htmlspecialchars($totalProducts); ?></p>
                </div>
            </section>

            <!-- Links Section -->
            <section class="quick-links">
                <h3>Quick Links</h3>
                <div class="link-card">
                    <a href="product.php">
                        <button>Manage Products</button>
                    </a>
                </div>
                <div class="link-card">
                    <a href="order_management.php">
                        <button>Manage Orders</button>
                    </a>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
