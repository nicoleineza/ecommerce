<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../css/services.css">
    <style>
        .adjust-quantity {
            display: inline-flex;
            align-items: center;
        }

        .adjust-quantity button {
            background-color: #ff6f61;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .adjust-quantity button:hover {
            background-color: #e55b4f;
        }

        .product-image {
            max-width: 50px; /* Set a maximum width for product images */
            height: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Add some styling */
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
                <li><a href="brands.php">Brands</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="cart.php">View Cart</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Your Cart</h1>

        <?php
        require_once '../Controllers/cart_controller.php';
        $cartController = new CartController();
        $customerId = $_SESSION['customer_id'];
        $cartItems = $cartController->getCartItems($customerId);

        if (!empty($cartItems)): ?>
            <table>
                <tr>
                    <th>Product Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <?php
                            // Ensure product image data is present
                            if (!empty($item['product_image']) && !empty($item['product_image_type'])):
                                $imageData = base64_encode($item['product_image']); 
                                echo "<img class='product-image' src='data:{$item['product_image_type']};base64,{$imageData}' alt='Product Image'>";
                            else:
                                echo "<img class='product-image' src='../assets/default-image.png' alt='Default Image'>"; // Default image
                            endif;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                        <td>$<?php echo htmlspecialchars($item['product_price']); ?></td>
                        <td>
                            <div class="adjust-quantity">
                                <button onclick="updateQuantity(<?php echo $item['p_id']; ?>, 'decrease')">−</button>
                                <input type="number" id="quantity-<?php echo $item['p_id']; ?>" value="<?php echo htmlspecialchars($item['qty']); ?>" min="1" style="width: 50px; text-align: center;">
                                <button onclick="updateQuantity(<?php echo $item['p_id']; ?>, 'increase')">+</button>
                            </div>
                        </td>
                        <td>$<?php echo htmlspecialchars($item['qty'] * $item['product_price']); ?></td>
                        <td>
                            <button onclick="deleteCartItem(<?php echo $item['p_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <h3>Total: $<?php echo array_sum(array_map(function($item) {
                return $item['qty'] * $item['product_price'];
            }, $cartItems)); ?></h3>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(productId, action) {
            const quantityInput = document.getElementById(`quantity-${productId}`);
            let currentQuantity = parseInt(quantityInput.value);

            if (action === 'increase') {
                currentQuantity++;
            } else if (action === 'decrease' && currentQuantity > 1) {
                currentQuantity--;
            }

            quantityInput.value = currentQuantity; // Update the input field

            // Update the cart in the database
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../action/cart_action.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("Cart updated successfully!");
                    location.reload(); // Refresh the page to see the changes
                } else {
                    alert("Failed to update cart. Please try again.");
                }
            };
            xhr.send(`action=update&product_id=${productId}&quantity=${currentQuantity}`);
        }

        function deleteCartItem(productId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../action/cart_action.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("Item removed from cart!");
                    location.reload(); // Refresh the page to see the changes
                } else {
                    alert("Failed to remove item from cart. Please try again.");
                }
            };
            xhr.send(`action=delete&product_id=${productId}`);
        }
    </script>
</body>
</html>
