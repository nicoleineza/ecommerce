<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="../css/services.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product-card h3 {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 1rem;
            color: #333;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .quantity-controls button {
            background-color: #ff6f61;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .quantity-controls button:hover {
            background-color: #e55b4f;
        }

        .quantity-controls input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h2><a href="index.php">mC</a></h2>
        </div>
        <nav>
            <ul>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="categories.php">Category</a></li>
                <li><a href="services.php">Products</a></li>
                <li><a href="brand.php">Brands</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h1>Available Products</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="product-grid">
            <?php 
            require_once '../Controllers/products_controller.php'; 
            $productsController = new ProductsController();
            $products = $productsController->getProducts();

            if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php 
                        if (!empty($product['product_image'])): 
                            $imageData = base64_encode($product['product_image']); 
                            echo "<img src='data:{$product['product_image_type']};base64,{$imageData}' alt='Product Image'>";
                        else: 
                            echo "<img src='../assets/default-image.png' alt='Default Image'>"; // Default image if no image available
                        endif; 
                        ?>
                        <h3><?php echo htmlspecialchars($product['product_title']); ?></h3>
                        <p>Price: $<?php echo htmlspecialchars($product['product_price']); ?></p>

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
                <p>No products found.</p>
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
//this is used to always update the current quantity of goods in the shop
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
   