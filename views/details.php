<?php
// Start the session
session_start();

// Include necessary controllers
require_once '../Controllers/productcontroller.php';
require_once '../Controllers/review_controller.php';

// Initialize variables
$product = null;
$reviews = [];

// Check if product_id is provided in the URL
if (isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']); 

    // Instantiate the controllers
    $productController = new ProductController();
    $reviewController = new ReviewController();

    // Fetch product details
    $product = $productController->fetchProductById($productId);

    // Fetch product reviews
    $reviews = $reviewController->fetchProductReviews($productId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['product_title']) : 'Product Details'; ?></title>
    <link rel="stylesheet" href="../css/details.css">
</head>
<body>
    <!-- Header with Navigation Bar -->
    <header>
        <div class="nav-bar">
            <h2><a href="shop.php" class="website-name">IMENA</a></h2>
            <div class="nav-links">
                <a href="shop.php" class="nav-link">Shop</a>
                <a href="cart.php" class="nav-link">Cart</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </header>

    <!-- Product Details Section -->
    <div class="product-details">
        <?php if ($product): ?>
            <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_title']); ?>">
            <h1><?php echo htmlspecialchars($product['product_title']); ?></h1>
            <p class="product-desc"><?php echo htmlspecialchars($product['product_desc']); ?></p>
            <p class="price">Price: $<?php echo htmlspecialchars($product['product_price']); ?></p>

            <div class="quantity-controls">
                <button onclick="adjustQuantity(<?php echo $product['product_id']; ?>, 'decrease')">−</button>
                <input type="number" id="quantity-<?php echo $product['product_id']; ?>" value="1" min="1" readonly>
                <button onclick="adjustQuantity(<?php echo $product['product_id']; ?>, 'increase')">+</button>
            </div>

            <button class="btn-add" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
        <?php else: ?>
            <p>Product not found. <a href="shop.php">Go back to shop.</a></p>
        <?php endif; ?>
    </div>

    <!-- Reviews Section -->
    <div class="product-reviews">
        <h2>Customer Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <p>Average Rating: 
                <?php 
                $averageRating = array_sum(array_column($reviews, 'rating')) / count($reviews);
                echo round($averageRating, 1) . " Stars";
                ?>
            </p>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p class="review-rating">Rating: <?php echo htmlspecialchars($review['rating']); ?> Stars</p>
                    <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet for this product.</p>
        <?php endif; ?>
    </div>

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
