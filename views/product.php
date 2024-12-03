<?php
session_start();

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: login.php");
    exit("You need to log in to access this page.");
}

require_once '../Controllers/productcontroller.php';
require_once '../Controllers/category_controller.php';
require_once '../Controllers/seller_controller.php';  // Include the seller controller to check verification status

$seller_id = $_SESSION['user_id']; // Retrieve the seller_id from the session

// Instantiate controllers
$productController = new ProductController();
$categoryController = new CategoryController();
$sellerController = new SellerController();

// Fetch products, categories, and verification status
$products = $productController->getAllProducts();
$categories = $categoryController->getCategories();
$verificationStatus = $sellerController->checkVerificationStatus($seller_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - IMENA</title>
    <link rel="stylesheet" href="../css/product.css">
</head>
<body>

<header>
    <h1>IMENA - Product Management</h1>
    <p>Your Product management page</p>
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
        <h2>Welcome, Seller ID: <?= htmlspecialchars($seller_id); ?></h2>

        <!-- Verification Status -->
        <section class="verification-status">
            <h3>Verification Status: <?= htmlspecialchars($verificationStatus); ?></h3>
            <?php if ($verificationStatus === 'Pending'): ?>
                <p>Your verification is currently pending. You cannot add or manage products until the verification process is completed.</p>
            <?php elseif ($verificationStatus !== 'Approved'): ?>
                <p>Your account is not approved yet. Please submit your verification document.</p>
            <?php else: ?>
                <p>Your verification is approved. You can now manage your products and orders.</p>
            <?php endif; ?>
        </section>

        <!-- Add Product Button -->
        <?php if ($verificationStatus === 'Approved'): ?>
            <button onclick="toggleForm('add_product_form')">Add a New Product</button>
        <?php endif; ?>

        <!-- Add Product Form (Initially Hidden) -->
        <div id="add_product_form">
            <h2>Add a New Product</h2>
            <form action="../action/productaction.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="seller_id" value="<?= htmlspecialchars($seller_id); ?>">
                <label for="product_cat">Category:</label>
                <select name="product_cat" id="product_cat" required>
                    <option value="" disabled selected>Select a Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['cat_id']); ?>">
                            <?= htmlspecialchars($category['cat_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="product_title">Product Title:</label>
                <input type="text" name="product_title" id="product_title" placeholder="Product Title" required>
                <label for="product_price">Price:</label>
                <input type="number" step="0.01" name="product_price" id="product_price" placeholder="Price" required>
                <label for="product_desc">Description:</label>
                <textarea name="product_desc" id="product_desc" placeholder="Description"></textarea>
                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" id="product_image" required>
                <label for="product_keywords">Keywords:</label>
                <input type="text" name="product_keywords" id="product_keywords" placeholder="Keywords">
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

        <!-- Product Display -->
        <h2>Your Products</h2>
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <?php if ($product['seller_id'] == $seller_id): ?>
                    <div class="product-card">
                        <?php
                        $imagePath = $product['product_image'];
                        if (!empty($imagePath) && file_exists('../' . $imagePath)) {
                            echo '<img src="../' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($product['product_title']) . '">';
                        } else {
                            echo '<img src="../images/no-image.png" alt="No image available">';
                        }
                        ?>
                        <h3><?= htmlspecialchars($product['product_title']); ?></h3>
                        <p><?= htmlspecialchars($product['product_price']); ?> USD</p>
                        <button onclick="showEditForm(<?= htmlspecialchars($product['product_id']); ?>, '<?= htmlspecialchars($product['product_title']); ?>', '<?= htmlspecialchars($product['product_price']); ?>', '<?= htmlspecialchars($product['product_desc']); ?>', '<?= htmlspecialchars($product['product_keywords']); ?>', '<?= htmlspecialchars($product['product_cat']); ?>')">Edit</button>
                        <button onclick="confirmDeleteProduct(<?= htmlspecialchars($product['product_id']); ?>)">Delete</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<!-- Edit Product Modal -->
<div id="edit_product_modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditForm()">&times;</span>
        <h2>Edit Product</h2>
        <form action="../action/productaction.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_product_id">
            <input type="hidden" name="seller_id" value="<?= htmlspecialchars($seller_id); ?>">
            <label for="edit_product_cat">Category:</label>
            <select name="product_cat" id="edit_product_cat" required>
                <option value="" disabled selected>Select a Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['cat_id']); ?>">
                        <?= htmlspecialchars($category['cat_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="edit_product_title">Product Title:</label>
            <input type="text" name="product_title" id="edit_product_title" placeholder="Product Title" required>
            <label for="edit_product_price">Price:</label>
            <input type="number" step="0.01" name="product_price" id="edit_product_price" placeholder="Price" required>
            <label for="edit_product_desc">Description:</label>
            <textarea name="product_desc" id="edit_product_desc" placeholder="Description"></textarea>
            <label for="edit_product_image">Product Image:</label>
            <input type="file" name="product_image" id="edit_product_image">
            <label for="edit_product_keywords">Keywords:</label>
            <input type="text" name="product_keywords" id="edit_product_keywords" placeholder="Keywords">
            <button type="submit" name="edit_product">Save Changes</button>
        </form>
    </div>
</div>

<script>
// JavaScript functions for managing forms and deletion
function toggleForm(formId) {
    const form = document.getElementById(formId);
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
}

function showEditForm(productId, productTitle, productPrice, productDesc, productKeywords, productCat) {
    document.getElementById('edit_product_id').value = productId;
    document.getElementById('edit_product_title').value = productTitle;
    document.getElementById('edit_product_price').value = productPrice;
    document.getElementById('edit_product_desc').value = productDesc;
    document.getElementById('edit_product_keywords').value = productKeywords;
    document.getElementById('edit_product_cat').value = productCat;
    document.getElementById('edit_product_modal').style.display = 'block';
}

function closeEditForm() {
    document.getElementById('edit_product_modal').style.display = 'none';
}

function confirmDeleteProduct(productId) {
    const confirmation = confirm("Are you sure you want to delete this product?");
    if (confirmation) {
        window.location.href = `../action/productaction.php?delete_product=${productId}`;
    }
}
</script>

</body>
</html>
