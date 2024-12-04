<?php
session_start();
require_once '../Controllers/productcontroller.php';
require_once '../Controllers/user_controller.php'; // Add the user controller to fetch sellers
$productsController = new ProductController();
$userController = new User(); // Assuming you have a controller for users

// Fetch filters and search term
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Fetch filtered and searched products
$products = $productsController->getProducts($minPrice, $maxPrice, $searchTerm);

// Fetch featured sellers
$sellers = $userController->get_users_by_role('seller'); // Corrected method name to match updated function
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMENA - Shop</title>
    <link rel="stylesheet" href="../css/shop.css">
    <style>
        /* Add some styles for the slideshow */
        .seller-slideshow {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
        }
        .slideshow-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .seller-card {
            width: 100%;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 20px;
            margin: 5px;
            text-align: center;
        }
        .seller-name {
            margin-top: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .slideshow-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
        }
        .prev-btn {
            left: 10px;
        }
        .next-btn {
            right: 10px;
        }
    </style>
</head>
<body>
    <!-- Motto Section -->
    <section class="hero">
        <h1>Welcome to <span>IMENA</span> Shop</h1>
        <p>Your ultimate destination for Rwandan luxury fashion.</p>
    </section>

    <!-- Header Section -->
    <header class="main-nav">
        <div class="container">
            <nav>
                <ul>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                    <li><a href="order_confirmation.php">My Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Featured Sellers Slideshow Section -->
    <section class="seller-slideshow">
        <h2>Featured Sellers</h2>
        <div class="slideshow-container">
            <?php foreach ($sellers as $seller): ?>
                <div class="seller-card">
                    <a href="seller_shop.php?seller_id=<?php echo $seller['user_id']; ?>" class="seller-link">
                        <p class="seller-name"><?php echo htmlspecialchars($seller['user_name']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="slideshow-btn prev-btn" onclick="moveSlide(-1)">❮</button>
        <button class="slideshow-btn next-btn" onclick="moveSlide(1)">❯</button>
    </section>

    <!-- Main Content -->
    <div class="container">
        <h2 class="section-title">Available Products</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Search Bar and Filters -->
        <div class="filters">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search for products..." value="<?php echo $searchTerm; ?>" aria-label="Search for products">
                <button type="submit" class="btn">Search</button>
            </form>
            <form method="GET" class="filter-form">
                <label for="min-price">Min Price:</label>
                <input type="number" id="min-price" name="min_price" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>" min="0">
                <label for="max-price">Max Price:</label>
                <input type="number" id="max-price" name="max_price" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" min="0">
                <button type="submit" class="btn">Apply</button>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php if (!empty($products)): ?>
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
            <?php else: ?>
                <p class="no-products">No products found for the selected criteria.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> IMENA. All rights reserved.</p>
    </footer>

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.seller-card');
        
        function moveSlide(step) {
            slideIndex += step;
            if (slideIndex < 0) slideIndex = slides.length - 1;
            if (slideIndex >= slides.length) slideIndex = 0;
            document.querySelector('.slideshow-container').style.transform = `translateX(-${slideIndex * 100}%)`;
        }

        // Automatic slideshow
        setInterval(function() {
            moveSlide(1);
        }, 3000); // Change slide every 3 seconds

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
