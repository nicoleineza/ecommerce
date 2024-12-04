<?php
session_start();
require_once '../Controllers/productcontroller.php';
require_once '../Controllers/user_controller.php'; // Add the user controller to fetch seller info
$productsController = new ProductController();
$userController = new UserAction(); // Updated to use UserAction class

// Get the seller ID from the URL
$sellerId = isset($_GET['seller_id']) ? (int)$_GET['seller_id'] : 0;

// Fetch the seller's information using the getSellerById method
$seller = $userController->getSellerById($sellerId);

// Fetch the seller's products
$products = $productsController->getProductsBySeller($sellerId);

// Handle if no seller is found
if (!$seller) {
    echo "<p>Seller not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Shop - <?php echo htmlspecialchars($seller['user_name']); ?></title>
    <link rel="stylesheet" href="../css/shop.css">
</head>
<body>
    <!-- Header Section -->
    <header class="main-nav">
        <div class="container">
            <nav>
                <ul>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Seller Details Section -->
    <section class="seller-details">
        <h1>Welcome to <?php echo htmlspecialchars($seller['user_name']); ?>'s Shop</h1>
        <!--<p><?php echo htmlspecialchars($seller['store_name']); ?></p> -->
    </section>

    <!-- Product Grid Section -->
    <div class="container">
        <h2 class="section-title">Products from <?php echo htmlspecialchars($seller['user_name']); ?></h2>

        <!-- Check if the seller has any products -->
        <?php if (empty($products)): ?>
            <p>This seller has no products available at the moment.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="details.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>" class="product-link">
                            <?php 
                            $imagePath = !empty($product['product_image']) ? '../' . $product['product_image'] : '../assets/default-image.png';
                            echo "<img src='{$imagePath}' alt='Product Image'>";
                            ?>
                            <h3><?php echo htmlspecialchars($product['product_title']); ?></h3>
                        </a>
                        <p class="product-price">$<?php echo htmlspecialchars($product['product_price']); ?></p>

                        <!-- Quantity Controls -->
                        <div class="quantity-controls">
                            <button onclick="adjustQuantity(<?php echo $product['product_id']; ?>, 'decrease')">−</button>
                            <input type="number" id="quantity-<?php echo $product['product_id']; ?>" value="1" min="1" readonly>
                            <button onclick="adjustQuantity(<?php echo $product['product_id']; ?>, 'increase')">+</button>
                        </div>

                        <button class="btn btn-add" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> IMENA. All rights reserved.</p>
    </footer>

    <script>
        function adjustQuantity(productId, action) {
            const quantityInput = document.getElementById(`quantity-${productId}`);
            let currentQuantity = parseInt(quantityInput.value);

            if (action === 'increase') {
                currentQuantity++;
            } else if (action === 'decrease' && currentQuantity > 1) {
                currentQuantity--;
            }

            quantityInput.value = currentQuantity; 
        }

        function addToCart(productId) {
            const quantity = document.getElementById(`quantity-${productId}`).value;

            if (quantity <= 0) {
                alert("Please enter a valid quantity.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../action/cart_action.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("Product added to cart successfully!");
                } else {
                    alert("Failed to add product to cart. Please try again.");
                }
            };
            xhr.send(`action=add&product_id=${productId}&quantity=${quantity}`);
        }
    </script>
</body>
</html>
