<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <style>
        .hidden { display: none; }
        .btn-submit, .btn-edit, .btn-delete, .btn-save { padding: 8px 12px; cursor: pointer; border: none; color: white; }
        .btn-submit { background-color: #28a745; }
        .btn-edit { background-color: #007bff; }
        .btn-save { background-color: #28a745; }
        .btn-delete { background-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
    <link rel="stylesheet" href="../css/services.css">
</head>
<header>
    <div class="nav-bar">
        <span class="website-name">MyCraft</span>
        <div class="nav-links">
    <a href="../lab 1/views/services.php" class="nav-link btn" id="login-link">Products</a>
    <a href="../lab 1/views/brand.php" class="nav-link btn" id="login-link">Brands</a>
    <a href="../lab 1/views/login.php" class="nav-link btn" id="login-link">Login</a>
    <a href="../lab 1/views/register.php" class="nav-link btn" id="register-link">Register</a>
    
    
</div>
    </div>
</header>
<body>

    <h1>Manage Categories</h1>

    <?php session_start(); ?>

    <!-- Display any message from the session -->
    <?php if (isset($_SESSION['message'])): ?>
        <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Button to Add Category -->
    <button class="btn-submit" onclick="toggleAddCategoryForm()">Add Category</button>

    <!-- Form to add a new category -->
    <form class="service-form hidden" id="add-category-form" action="../action/add_category_action.php" method="POST">
        <div class="form-group">
            <label for="cat_name">Category Name:</label>
            <input type="text" name="cat_name" id="cat_name" required>
        </div>
        <button type="submit" class="btn-submit">Add Category</button>
    </form>

    <h2>Existing Categories</h2>

    <!-- Existing Categories Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Actions</th>
        </tr>
        <?php 
        require_once '../Controllers/category_controller.php';
        $categoryController = new CategoryController();
        $categories = $categoryController->getCategories();
        
        if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['cat_id']); ?></td>
                    <td>
                        <span id="cat-name-<?php echo $category['cat_id']; ?>"><?php echo htmlspecialchars($category['cat_name']); ?></span>
                        <input type="text" id="edit-cat-name-<?php echo $category['cat_id']; ?>" class="hidden" value="<?php echo htmlspecialchars($category['cat_name']); ?>">
                    </td>
                    <td>
                        <button class="btn-edit" onclick="toggleEdit(<?php echo $category['cat_id']; ?>)">Edit</button>
                        <button class="btn-save hidden" onclick="updateCategory(<?php echo $category['cat_id']; ?>)" id="save-button-<?php echo $category['cat_id']; ?>">Save</button>
                        <form action="../action/delete_category_action.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_category" value="<?php echo $category['cat_id']; ?>">
                            <input type="submit" class="btn-delete" value="Delete" onclick="return confirm('Are you sure you want to delete this category?');">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No categories found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script>
        // Toggle the visibility of the category addition form
        function toggleAddCategoryForm() {
            const form = document.getElementById('add-category-form');
            form.classList.toggle('hidden');
        }

        // Toggle the edit form for categories
        function toggleEdit(catId) {
            const catNameSpan = document.getElementById(`cat-name-${catId}`);
            const editCatNameInput = document.getElementById(`edit-cat-name-${catId}`);
            const saveButton = document.getElementById(`save-button-${catId}`);

            if (editCatNameInput.classList.contains("hidden")) {
                editCatNameInput.classList.remove("hidden");
                saveButton.classList.remove("hidden");
                catNameSpan.classList.add("hidden");
            } else {
                editCatNameInput.classList.add("hidden");
                saveButton.classList.add("hidden");
                catNameSpan.classList.remove("hidden");
            }
        }

        // Update category via AJAX
        function updateCategory(catId) {
            const catNameValue = document.getElementById(`edit-cat-name-${catId}`).value;

            if (catNameValue) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/edit_category_action.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        location.reload();
                    } else {
                        alert("Failed to update category. Please try again.");
                    }
                };
                xhr.send(`cat_id=${catId}&cat_name=${encodeURIComponent(catNameValue)}`);
            } else {
                alert("Category name cannot be empty.");
            }
        }
    </script>

</body>
</html>
