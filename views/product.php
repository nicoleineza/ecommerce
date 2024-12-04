<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    header("Location: login.php");
    exit("You need to log in to access this page.");
}

require_once '../Controllers/productcontroller.php';
require_once '../Controllers/category_controller.php';
require_once '../Controllers/seller_controller.php';  // seller controller to check verification status

$seller_id = $_SESSION['user_id']; 

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
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .modal .close:hover,
        .modal .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal input[type="text"],
        .modal input[type="number"],
        .modal select,
        .modal textarea,
        .modal button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .modal button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }

        .modal button:hover {
            background-color: #45a049;
        }

        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .product-card button {
            background-color: #ff5733;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .product-card button:hover {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>

<header>
    <h1>IMENA - Product Management</h1>
    <p>Your Product Management Page</p>
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

        
        <?php if ($verificationStatus === 'Approved'): ?>
            <button onclick="toggleForm('add_product_form')">Add a New Product</button>
        <?php endif; ?>

        <div id="add_product_form" style="display:none;">
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
