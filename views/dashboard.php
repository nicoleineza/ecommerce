<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: login.php");
    exit("You need to log in to access this page.");
}

require_once '../Controllers/order_controller.php';
require_once '../Controllers/productcontroller.php';
require_once '../Controllers/seller_controller.php';  

$seller_id = $_SESSION['user_id']; 

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
    $pendingOrders = $orderController->fetchSellerOrders($seller_id); 
    $totalProducts = count($productController->getAllProducts()); 
}

// Fetch seller stories
$sellerStories = $sellerController->getSellerStory($seller_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
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
                    <li><a href="transaction.php">My Finances</a></li>
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
                <p>Your account is not approved yet. Please submit your trade licence to start selling.</p>
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

            <!-- Seller Stories Section 
            <section class="seller-stories">
                <h3>Your Stories</h3>
                <?php if ($sellerStories): ?>
                    <ul>
                        <?php foreach ($sellerStories as $story): ?>
                            <li>
                                <h4><?= htmlspecialchars($story['title']); ?></h4>
                                <p><?= nl2br(htmlspecialchars($story['content'])); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Share your entrepreneurial journey with your customers!</p>
                <?php endif; ?>
                
                <h4>Add a New Story</h4>
                <form action="../action/seller_action.php?action=add_story" method="POST">
                    <label for="content">Story Content:</label>
                    <textarea name="content" rows="4" required></textarea><br><br>

                    <button type="submit">Add Story</button>
                </form>
            </section>-->
        <?php endif; ?>
    </main>
</div>

</body>
</html>
