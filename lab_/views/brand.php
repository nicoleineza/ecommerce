<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management</title>
    <!-- Link to CSS -->
    <link rel="stylesheet" href="../css/services.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <h2><a href="index.php">My E-Commerce</a></h2> <!-- Website Name -->
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="services.php">Products</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Section -->
    <div class="container">
        <h1>Manage Brands</h1>

        <?php session_start(); ?>

        <!-- Display any message from the session -->
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success-message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Form to add a new brand -->
        <form class="service-form" action="../action/add_brand_action.php" method="POST">
            <div class="form-group">
                <label for="brand_name">Brand Name:</label>
                <input type="text" name="brand_name" id="brand_name" required>
            </div>
            <button type="submit" class="btn-submit">Add Brand</button>
        </form>

        <h2>Existing Brands</h2>

        <!-- Existing Brands Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Brand Name</th>
                <th>Actions</th>
            </tr>
            <?php 
            require_once '../Controllers/general_controller.php'; 
            $generalController = new GeneralController();
            $brands = $generalController->getBrands(); 
            
            if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($brand['brand_id']); ?></td>
                        <td>
                            <span id="brand-name-<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></span>
                            <input type="text" id="edit-brand-name-<?php echo $brand['brand_id']; ?>" class="hidden" value="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                        </td>
                        <td>
                            <button class="btn-edit" onclick="toggleEdit(<?php echo $brand['brand_id']; ?>)">Edit</button>
                            <button class="btn-save hidden" id="save-button-<?php echo $brand['brand_id']; ?>" onclick="updateBrand(<?php echo $brand['brand_id']; ?>)">Save</button>
                            <form action="../action/delete_brand_action.php" method="POST" style="display:inline;">
                                <input type="hidden" name="delete_brand" value="<?php echo $brand['brand_id']; ?>">
                                <input type="submit" class="btn-delete" value="Delete" onclick="return confirm('Are you sure you want to delete this brand?');">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No brands found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <script>
        // Function to toggle the edit input field and button
        function toggleEdit(brandId) {
            const brandNameSpan = document.getElementById(`brand-name-${brandId}`);
            const editInput = document.getElementById(`edit-brand-name-${brandId}`);
            const saveButton = document.getElementById(`save-button-${brandId}`);

            if (editInput.classList.contains("hidden")) {
                // Show input and hide the span
                editInput.classList.remove("hidden");
                brandNameSpan.classList.add("hidden");
                saveButton.classList.remove("hidden"); // Show save button
            } else {
                // Hide input and show the span
                editInput.classList.add("hidden");
                brandNameSpan.classList.remove("hidden");
                saveButton.classList.add("hidden"); // Hide save button
            }
        }

        // Function to update the brand name
        function updateBrand(brandId) {
            const editInput = document.getElementById(`edit-brand-name-${brandId}`).value;

            if (editInput) {
                // Send the update request to the server
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../action/edit_brand_action.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Reload the page after the brand is updated
                        location.reload();
                    } else {
                        alert("Failed to update brand. Please try again.");
                    }
                };
                xhr.send(`brand_id=${brandId}&brand_name=${encodeURIComponent(editInput)}`);
            } else {
                alert("Brand name cannot be empty.");
            }
        }
    </script>

</body>
</html>
