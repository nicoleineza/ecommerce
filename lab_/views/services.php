<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="../css/services.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <h2><a href="index.php">mC</a></h2>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Category</a></li>
                <li><a href="services.php">Products</a></li>
                <li><a href="brand.php">Brands</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Section -->
    <div class="container">
        <h1>Manage Products</h1>

        <!-- Display any message from the session -->
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Form to add a new product -->
        <form class="service-form" action="../action/add_product_action.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_title">Product Title:</label>
                <input type="text" name="product_title" id="product_title" required>
            </div>

            <div class="form-group">
                <label for="product_price">Product Price:</label>
                <input type="number" name="product_price" id="product_price" required step="0.01">
            </div>

            <div class="form-group">
                <label for="product_desc">Product Description:</label>
                <textarea name="product_desc" id="product_desc" required></textarea>
            </div>

            <div class="form-group">
                <label for="product_brand">Product Brand:</label>
                <select name="product_brand" id="product_brand" required>
                    <?php
                    require_once '../Controllers/general_controller.php';
                    $generalController = new GeneralController();
                    $brands = $generalController->getBrands();
                    foreach ($brands as $brand) {
                        echo "<option value=\"{$brand['brand_id']}\">" . htmlspecialchars($brand['brand_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="product_cat">Product Category:</label>
                <select name="product_cat" id="product_cat" required>
                    <?php
                    require_once '../Controllers/category_controller.php';
                    $categoryController = new CategoryController();
                    $categories = $categoryController->getCategories();
                    foreach ($categories as $category) {
                        echo "<option value=\"{$category['cat_id']}\">" . htmlspecialchars($category['cat_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" id="product_image" accept="image/*" required>
            </div>

            <button type="submit" class="btn-submit">Add Product</button>
        </form>

        <h2>Existing Products</h2>

        <!-- Existing Products Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Product Image</th>
                <th>Product Title</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php 
            require_once '../Controllers/products_controller.php'; 
            $productsController = new ProductsController();
            $products = $productsController->getProducts();
            
            if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                        <td>
                            <?php 
                            // Display image if it's available
                            if (!empty($product['product_image'])): 
                                $imageData = base64_encode($product['product_image']); // Encode the image data
                                echo "<img src='data:{$product['product_image_type']};base64,{$imageData}' alt='Product Image' width='50' height='50'>";
                            else: 
                                echo "No image available";
                            endif; 
                            ?>
                        </td>
                        <td>
                            <span id="product-title-<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_title']); ?></span>
                            <input type="text" id="edit-product-title-<?php echo $product['product_id']; ?>" class="hidden" value="<?php echo htmlspecialchars($product['product_title']); ?>">
                        </td>
                        <td>
                            <span id="product-price-<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_price']); ?></span>
                            <input type="number" id="edit-product-price-<?php echo $product['product_id']; ?>" class="hidden" value="<?php echo htmlspecialchars($product['product_price']); ?>">
                        </td>
                        <td>
                            <button class="btn-edit" onclick="toggleEdit(<?php echo $product['product_id']; ?>)">Edit</button>
                            <button class="btn-save hidden" onclick="updateProduct(<?php echo $product['product_id']; ?>)" id="save-button-<?php echo $product['product_id']; ?>">Save</button>
                            <form action="../action/delete_product_action.php" method="POST" style="display:inline;">
                                <input type="hidden" name="delete_product" value="<?php echo $product['product_id']; ?>">
                                <input type="submit" class="btn-delete" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No products found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <script>
        function toggleEdit(productId) {
            const titleSpan = document.getElementById(`product-title-${productId}`);
            const priceSpan = document.getElementById(`product-price-${productId}`);
            const editTitleInput = document.getElementById(`edit-product-title-${productId}`);
            const editPriceInput = document.getElementById(`edit-product-price-${productId}`);
            const saveButton = document.getElementById(`save-button-${productId}`);

            if (editTitleInput.classList.contains("hidden")) {
                editTitleInput.classList.remove("hidden");
                editPriceInput.classList.remove("hidden");
                saveButton.classList.remove("hidden");
                titleSpan.classList.add("hidden");
                priceSpan.classList.add("hidden");
            } else {
                editTitleInput.classList.add("hidden");
                editPriceInput.classList.add("hidden");
                saveButton.classList.add("hidden");
                titleSpan.classList.remove("hidden");
                priceSpan.classList.remove("hidden");
            }
        }

        function updateProduct(productId) {
            const titleValue = document.getElementById(`edit-product-title-${productId}`).value;
            const priceValue = document.getElementById(`edit-product-price-${productId}`).value;

            if (titleValue && priceValue) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/edit_product_action.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    } else {
                        alert("Failed to update product. Please try again.");
                    }
                };
                xhr.send(`product_id=${productId}&product_title=${encodeURIComponent(titleValue)}&product_price=${encodeURIComponent(priceValue)}`);
            } else {
                alert("All fields are required.");
            }
        }
    </script>

</body>
</html>
