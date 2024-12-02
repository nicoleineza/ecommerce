<?php
session_start();
require_once '../Controllers/productcontroller.php';
$productsController = new ProductController();

// Fetch filters and search term
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Fetch filtered and searched products
$products = $productsController->getProducts($minPrice, $maxPrice, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMENA -Shop</title>
    <link rel="stylesheet" href="../css/shop.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <h1><a href="shop.php">IMENA</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>

    <!-- Motto Section -->
    <div class="motto">
        <p>Buy from Rwanda, Ship to the World</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Available Products</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search for products..." value="<?php echo $searchTerm; ?>" aria-label="Search for products">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Filter Form -->
        <form class="filter-form" method="GET" action="">
            <label for="min-price">Min Price:</label>
            <input type="number" id="min-price" name="min_price" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>" min="0">
            <label for="max-price">Max Price:</label>
            <input type="number" id="max-price" name="max_price" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" min="0">
            <button type="submit">Filter</button>
        </form>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="details.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>" class="product-link">
                            <?php 
                            $imagePath = !empty($product['product_image']) ? '../' . $product['product_image'] : '../assets/default-image.png';
                            if (file_exists($imagePath)): 
                                echo "<img src='{$imagePath}' alt='Product Image'>";
                            else: 
                                echo "<img src='../assets/default-image.png' alt='Default Image'>";
                            endif; 
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

                        <button class="btn-add" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found for the selected criteria.</p>
            <?php endif; ?>
        </div>
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
